<?php declare(strict_types=1);

namespace Symplify\Monorepo\Worker;

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
    private $currentFileInfoCount = 0;

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
        $this->currentFileInfoCount = 0;

        $fileInfosChunks = $this->splitToChunks($fileInfos);
        $totalFileCount = count($fileInfos);

        foreach ($fileInfosChunks as $fileInfosChunk) {
            if ($totalFileCount >= self::CHUNK_SIZE) {
                $this->currentFileInfoCount += count($fileInfosChunk);
                $this->symfonyStyle->warning(sprintf(
                    'Processing chunk %d/%d',
                    $this->currentFileInfoCount,
                    $totalFileCount
                ));
            }

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
                if ($totalFileCount < self::CHUNK_SIZE) {
                    continue;
                }

                if ($incrementalOutput = $process->getIncrementalOutput()) {
                    $this->symfonyStyle->note($this->clearExtraEmptyLines($incrementalOutput));

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

    /**
     * This is needed due to long CLI arguments overflow error
     * @param SplFileInfo[] $fileInfos
     * @return SplFileInfo[][]
     */
    private function splitToChunks(array $fileInfos): array
    {
        return array_chunk($fileInfos, self::CHUNK_SIZE, true);
    }

    private function clearExtraEmptyLines(string $content): string
    {
        return preg_replace('#(\r?\n){2,}#', PHP_EOL, $content);
    }
}
