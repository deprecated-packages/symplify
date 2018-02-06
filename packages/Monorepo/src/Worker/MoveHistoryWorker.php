<?php declare(strict_types=1);

namespace Symplify\Monorepo\Worker;

use Nette\Utils\Strings;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;
use Symplify\Monorepo\Configuration\BashFiles;
use Symplify\Monorepo\Exception\Worker\MoveWithHistoryException;

final class MoveHistoryWorker
{
    /**
     * Limit of file count to prevent CLI line length overflow.
     *
     * @var int
     */
    private const CHUNK_SIZE = 200;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var int
     */
    private $lastMvHistoryStepCount = 0;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    /**
     * @param SplFileInfo[] $fileInfos
     */
    public function prependHistoryToNewPackageFiles(
        array $fileInfos,
        string $monorepoDirectory,
        string $packageSubdirectory
    ): void {
        // reset counter
        $this->lastMvHistoryStepCount = 0;

        $fileInfosChunks = $this->splitToChunks($fileInfos);
        $totalStepCount = count($fileInfos) * $this->getCommitCount($monorepoDirectory);

        $this->symfonyStyle->progressStart($totalStepCount);

        foreach ($fileInfosChunks as $fileInfosChunk) {
            // @todo ProcessFactory
            $processInput = $this->createGitMoveWithHistoryProcessInput($fileInfosChunk, $packageSubdirectory);
            $process = new Process($processInput, $monorepoDirectory, null, null, null);
            $process->start();

            while ($process->isRunning()) {
                // check errors
                if ($errorOutput = $process->getIncrementalErrorOutput()) {
                    throw new MoveWithHistoryException($errorOutput);
                }

                // show process
                if ($incrementalOutput = $process->getIncrementalOutput()) {
                    $progressIncrement = $this->extractProgress($incrementalOutput);

                    $this->symfonyStyle->progressAdvance($progressIncrement);

                    // iterate slowly
                    sleep(10);
                }
            }
        }

        $this->symfonyStyle->newLine(2);
    }

    /**
     * @param SplFileInfo[] $fileInfos
     * @return mixed[]
     */
    private function createGitMoveWithHistoryProcessInput(array $fileInfos, string $packageSubdirectory): array
    {
        $processInput = [realpath(BashFiles::MOVE_WITH_HISTORY)];
        foreach ($fileInfos as $fileInfo) {
            $processInput[] = sprintf(
                '%s=%s',
                $fileInfo->getRelativePathname(),
                $packageSubdirectory . '/' . $fileInfo->getRelativePathname()
            );
        }

        return $processInput;
    }

    private function extractProgress(string $output): int
    {
        $matches = Strings::matchAll($output, '#(?<current>[0-9]+)\/(?<total>[0-9]+)#');
        if (! count($matches)) {
            return 0;
        }

        $lastMatch = array_pop($matches);

        $progressIncrement = $lastMatch['current'] - $this->lastMvHistoryStepCount;

        $this->lastMvHistoryStepCount = $lastMatch['current'];

        return $progressIncrement;
    }

    private function getCommitCount(string $repositoryDirectory): int
    {
        $process = new Process('git rev-list --count master', $repositoryDirectory);
        $process->run();

        return (int) $process->getOutput();
    }

    /**
     * This is needed due to long CLI arguments overflow error
     * @param SplFileInfo[] $fileInfos
     * @return SplFileInfo[][]
     */
    private function splitToChunks(array $fileInfos): array
    {
        return array_chunk($fileInfos, self::CHUNK_SIZE, true);
    }
}
