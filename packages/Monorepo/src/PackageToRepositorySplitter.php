<?php declare(strict_types=1);

namespace Symplify\Monorepo;

use Nette\Utils\Strings;
use Spatie\Async\Pool;
use Spatie\Async\Process\ParallelProcess;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Symplify\Monorepo\Configuration\RepositoryGuard;
use Throwable;

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
     * @var Pool
     */
    private $pool;

    public function __construct(SymfonyStyle $symfonyStyle, RepositoryGuard $repositoryGuard)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->repositoryGuard = $repositoryGuard;
        $this->pool = Pool::create();
    }

    /**
     * @param mixed[] $splitConfig
     */
    public function splitDirectoriesToRepositories(array $splitConfig): void
    {
        $theMostRecentTag = $this->getMostRecentTag();

        $i = 0;
        foreach ($splitConfig as $localSubdirectory => $remoteRepository) {
            if ($this->symfonyStyle->isDebug()) {
                $this->symfonyStyle->note(sprintf(
                    'Checking if split for "%s" is needed for "%s" repository',
                    $localSubdirectory,
                    $remoteRepository
                ));
            }

            if ($this->isRemoteUpToDate($localSubdirectory, $remoteRepository)) {
                continue;
            }

            $process = $this->createSubsplitPublishProcess($theMostRecentTag, $localSubdirectory, $remoteRepository);
            $parallelProcess = ParallelProcess::create($process, ++$i);

            // error
            $parallelProcess->catch(function (Throwable $throwable): void {
                if (Strings::contains($throwable->getMessage(), '[DONE]')) {
                    // false positive - often success
                    $this->symfonyStyle->success(trim($throwable->getMessage()));
                } else {
                    $this->symfonyStyle->error($throwable->getMessage());
                }
            });

            // success
            $parallelProcess->then(function (string $output): void {
                $this->symfonyStyle->success($output);
            });

            $this->pool->add($parallelProcess);
        }

        $this->pool->wait();
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

        $process = new Process(sprintf(
            'git subsplit publish --heads=master %s %s',
            $this->hasRepositoryTag(
                $remoteRepository,
                $theMostRecentTag
            ) ? '' : sprintf('--tags=%s', $theMostRecentTag),
            sprintf('%s:%s', $localSubdirectory, $remoteRepository)
        ));

        if ($this->symfonyStyle->isDebug()) {
            $this->symfonyStyle->note($process->getCommandLine());
        }

        return $process;
    }

    private function hasRepositoryTag(string $remoteRepository, string $theMostRecentTag): bool
    {
        $process = new Process(sprintf('git ls-remote %s refs/tags/%s', $remoteRepository, $theMostRecentTag));
        $process->run();

        return (bool) $process->getOutput();
    }

    private function getUnixDateTimeForLastCommit(): string
    {
        $process = new Process('git log -1 --pretty=format:%ct');
        $process->run();

        return trim($process->getOutput());
    }

    /**
     * @wip
     */
    private function isRemoteUpToDate(string $localSubdirectory, string $remoteRepository): bool
    {
        $localSubdirectoryLastCommitDateTime = $this->getUnixDateTimeForLastCommit();
        $lastCommitHashForRemoteRepository = $this->getLastCommitHashForRemoteRepository($remoteRepository);

        return false;
    }

    /**
     * @wip
     */
    private function getLastCommitHashForRemoteRepository(string $remoteRepository): string
    {
        $process = new Process(sprintf('git ls-remote %s refs/heads/master', $remoteRepository));
        $process->run();

        return trim($process->getOutput());
    }
}
