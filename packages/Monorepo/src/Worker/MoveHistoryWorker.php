<?php declare(strict_types=1);

namespace Symplify\Monorepo\Worker;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
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
    private const CHUNK_SIZE = 300;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    public function prependHistoryToNewPackageFiles(
        Finder $finder,
        string $monorepoDirectory,
        string $packageSubdirectory
    ): void {
        $fileInfos = iterator_to_array($finder->getIterator());

        // this is needed due to long CLI arguments overflow error
        $fileInfosChunks = array_chunk($fileInfos, self::CHUNK_SIZE, true);

        $this->symfonyStyle->progressStart(count($fileInfos));

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
                    $this->symfonyStyle->note($this->clearExtraEmptyLines($incrementalOutput));
                    // iterate slowly
                    sleep(10);
                }
            }

            $this->symfonyStyle->progressAdvance(count($fileInfosChunk));
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

    private function clearExtraEmptyLines(string $content): string
    {
        return preg_replace('#(\r?\n){2,}#', PHP_EOL, $content);
    }
}
