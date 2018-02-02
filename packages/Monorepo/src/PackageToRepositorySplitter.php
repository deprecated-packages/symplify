<?php declare(strict_types=1);

namespace Symplify\Monorepo;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Symplify\Monorepo\Configuration\RepositoryGuard;
use Symplify\Monorepo\Exception\Worker\PackageToRepositorySplitException;
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
    private $processPool = [];

    /**
     * @var SplitProcessInfo[]
     */
    private $processInfos = [];

    public function __construct(SymfonyStyle $symfonyStyle, RepositoryGuard $repositoryGuard)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->repositoryGuard = $repositoryGuard;
    }

    /**
     * @param mixed[] $splitConfig
     */
    public function splitDirectoriesToRepositories(array $splitConfig): void
    {
        $theMostRecentTag = $this->getMostRecentTag();

        foreach ($splitConfig as $localSubdirectory => $remoteRepository) {
            $process = $this->createSubsplitPublishProcess($theMostRecentTag, $localSubdirectory, $remoteRepository);
            $this->symfonyStyle->note('Running: ' . $process->getCommandLine());
            $process->start();

            $this->processPool[] = $process;
            $this->processInfos[] = SplitProcessInfo::createFromProcessLocalDirectoryAndRemoteRepository(
                $process,
                $localSubdirectory,
                $remoteRepository
            );
        }

        $this->symfonyStyle->success(sprintf('Running %d jobs asynchronously', count($pool)));

        while (count($this->processPool) > 0) {
            foreach ($this->processPool as $i => $runningProcess) {
                if (! $runningProcess->isRunning()) {
                    unset($this->processPool[$i]);
                }
            }

            // check every second
            sleep(1);
        }

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

    private function getMostRecentTag(): string
    {
        $process = new Process('git tag -l --sort=committerdate');
        $process->run();
        $tags = $process->getOutput();
        $tagList = explode(PHP_EOL, trim($tags));

        return (string) array_pop($tagList);
    }

    private function createSubsplitPublishProcess(
        string $theMostRecentTag,
        string $localSubdirectory,
        string $remoteRepository
    ): Process {
        $this->repositoryGuard->ensureIsRepository($remoteRepository);

        $commandLine = sprintf(
            'git subsplit publish --heads=master --tags=%s %s',
            $theMostRecentTag,
            sprintf('%s:%s', $localSubdirectory, $remoteRepository)
        );

        return new Process($commandLine, null, null, null, null);
    }
}
