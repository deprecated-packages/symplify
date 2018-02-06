<?php declare(strict_types=1);

namespace Symplify\Monorepo;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Symplify\Monorepo\Configuration\BashFiles;
use Symplify\Monorepo\Configuration\RepositoryGuard;
use Symplify\Monorepo\Exception\Worker\PackageToRepositorySplitException;
use Symplify\Monorepo\Filesystem\FileSystemGuard;
use Symplify\Monorepo\Process\SplitProcessInfo;

final class PackageToRepositorySplitter
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var RepositoryGuard
     */
    private $repositoryGuard;

    /**
     * @var Process[]
     */
    private $activeProcesses = [];

    /**
     * @var SplitProcessInfo[]
     */
    private $processInfos = [];

    /**
     * @var FileSystemGuard
     */
    private $fileSystemGuard;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        RepositoryGuard $repositoryGuard,
        FileSystemGuard $fileSystemGuard
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->repositoryGuard = $repositoryGuard;
        $this->fileSystemGuard = $fileSystemGuard;
    }

    /**
     * @param mixed[] $splitConfig
     */
    public function splitDirectoriesToRepositories(array $splitConfig, string $cwd): void
    {
        $theMostRecentTag = $this->getMostRecentTag($cwd);

        foreach ($splitConfig as $localSubdirectory => $remoteRepository) {
            $this->fileSystemGuard->ensureDirectoryExists($localSubdirectory);

            $process = $this->createSubsplitPublishProcess(
                $theMostRecentTag,
                $localSubdirectory,
                $remoteRepository,
                $cwd
            );
            $this->symfonyStyle->note('Running: ' . $process->getCommandLine());
            $process->start();

            $this->activeProcesses[] = $process;
            $this->processInfos[] = SplitProcessInfo::createFromProcessLocalDirectoryAndRemoteRepository(
                $process,
                $localSubdirectory,
                $remoteRepository
            );
        }

        $this->symfonyStyle->success(sprintf('Running %d jobs asynchronously', count($this->activeProcesses)));

        while (count($this->activeProcesses)) {
            foreach ($this->activeProcesses as $i => $runningProcess) {
                if (! $runningProcess->isRunning()) {
                    unset($this->activeProcesses[$i]);
                }
            }

            // check every second
            sleep(1);
        }

        $this->reportFinishedProcesses();
    }

    private function getMostRecentTag(string $cwd): string
    {
        $process = new Process('git tag -l --sort=committerdate', $cwd);
        $process->run();
        $tags = $process->getOutput();
        $tagList = explode(PHP_EOL, trim($tags));

        return (string) array_pop($tagList);
    }

    private function createSubsplitPublishProcess(
        string $theMostRecentTag,
        string $localSubdirectory,
        string $remoteRepository,
        string $cwd
    ): Process {
        $this->repositoryGuard->ensureIsRepository($remoteRepository);

        $commandLine = sprintf(
            '%s publish --heads=master %s %s:%s',
            BashFiles::SUBSPLIT,
            $theMostRecentTag ? sprintf('--tags=%s', $theMostRecentTag) : '',
            $localSubdirectory,
            $remoteRepository
        );

        return new Process($commandLine, $cwd, null, null, null);
    }

    private function reportFinishedProcesses(): void
    {
        foreach ($this->processInfos as $processInfo) {
            $process = $processInfo->getProcess();
            if (! $process->isSuccessful()) {
                throw new PackageToRepositorySplitException($process->getErrorOutput());
            }

            $this->symfonyStyle->success(sprintf(
                'Push of "%s" directory to "%s" repository was successful',
                $processInfo->getLocalDirectory(),
                $processInfo->getRemoteRepository()
            ));
        }
    }
}
