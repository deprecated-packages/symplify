<?php declare(strict_types=1);

namespace Symplify\Monorepo\Worker;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;
use Symplify\Monorepo\Exception\Worker\MoveWithHistoryException;

final class MoveHistoryWorker
{
    /**
     * @var int
     */
    private const CHUNK_SIZE = 100;

    /**
     * @var string
     */
    private const GIT_MV_WITH_HISTORY_BASH_FILE = __DIR__ . '/../bash/git-mv-with-history.sh';

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
        foreach ($fileInfosChunks as $fileInfosChunk) {
            $this->processFileInfosChunk($fileInfosChunk, $monorepoDirectory, $packageSubdirectory);
        }
    }

    /**
     * @param SplFileInfo[] $fileInfos
     */
    private function processFileInfosChunk(array $fileInfos, string $monorepoDirectory, string $packageSubdirectory): void
    {
        $processInput = $this->createGitMoveWithHistoryProcessInput($fileInfos, $packageSubdirectory);

        $process = new Process($processInput, $monorepoDirectory, null, null, null);
        $process->start();
        while ($process->isRunning()) {
            // waiting for process to finish
            $this->symfonyStyle->write($process->getOutput());
        }
        $process->wait();

        if (! $process->isSuccessful()) {
            throw new MoveWithHistoryException($process->getErrorOutput());
        }
    }

    /**
     * @param SplFileInfo[] $fileInfos
     * @return mixed[]
     */
    private function createGitMoveWithHistoryProcessInput(array $fileInfos, string $packageSubdirectory): array
    {
        $processInput = [self::GIT_MV_WITH_HISTORY_BASH_FILE];
        foreach ($fileInfos as $fileInfo) {
            $processInput[] = sprintf(
                '%s=%s',
                $fileInfo->getRelativePathname(),
                $packageSubdirectory . '/' . $fileInfo->getRelativePathname()
            );
        }

        return $processInput;
    }
}
