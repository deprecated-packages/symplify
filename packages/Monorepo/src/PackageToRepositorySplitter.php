<?php declare(strict_types=1);

namespace Symplify\Monorepo;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Symfony\Component\Stopwatch\Stopwatch;
use Symplify\Monorepo\Configuration\RepositoryGuard;
use Symplify\Monorepo\Exception\Worker\PackageToRepositorySplitException;

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

        $stopwatch = new Stopwatch();
        $stopwatch->start('hey');

        /** @var Process[] $pool */
        $pool = [];

        /** @var Process[] $allProcesses */
        $allProcesses = [];

        foreach ($splitConfig as $localSubdirectory => $remoteRepository) {
            $process = $this->createSubsplitPublishProcess($theMostRecentTag, $localSubdirectory, $remoteRepository);

            $this->symfonyStyle->note('Running: ' . $process->getCommandLine());

            $process->start();

            $pool[] = $allProcesses[$localSubdirectory] = $process;
        }

        $this->symfonyStyle->success(sprintf('Running %d jobs asynchronously', count($pool)));

        while (count($pool) > 0) {
            foreach ($pool as $i => $runningProcess) {
                if (! $runningProcess->isRunning())  {
                    unset($pool[$i]);
                }
            }

            // check every second
            sleep(1);
        }

        foreach ($allProcesses as $localSubdirectory => $process) {
            if (! $process->isSuccessful()) {
                throw new PackageToRepositorySplitException($process->getErrorOutput());
            }

            $this->symfonyStyle->success(sprintf(
                'Push of "%s" directory was successful',
                $localSubdirectory
            ));
        }

        $total = $stopwatch->stop('hey');
        dump((string) $total);
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
