<?php declare(strict_types=1);

namespace Symplify\Monorepo;

use GitWrapper\GitWorkingCopy;
use Spatie\Async\Pool;
use Spatie\Async\Process\ParallelProcess;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Symfony\Component\Stopwatch\Stopwatch;
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
    public function splitDirectoriesToRepositories(GitWorkingCopy $gitWorkingCopy, array $splitConfig): void
    {
        $theMostRecentTag = $this->getMostRecentTag($gitWorkingCopy);

        $i = 0;
        foreach ($splitConfig as $localSubdirectory => $remoteRepository) {
            // @todo: check is split is needed! - check tag and check commit publish

            $process = $this->createProcess($theMostRecentTag, $localSubdirectory, $remoteRepository);
            $parallelProcess = ParallelProcess::create($process, ++$i);

            // error
            $parallelProcess->catch(function (Throwable $throwable): void {
                // @todo: false positive - often success
                $this->symfonyStyle->error($throwable->getMessage());
            });

            // success
            $parallelProcess->then(function (string $output): void {
                $this->symfonyStyle->success($output);
            });

            $this->pool->add($parallelProcess);
        }

        $this->pool->wait();
    }

    private function getMostRecentTag(GitWorkingCopy $gitWorkingCopy): string
    {
        $tags = $gitWorkingCopy->tag('-l', '--sort=committerdate');
        $tagList = explode(PHP_EOL, trim($tags));

        return (string) array_pop($tagList);
    }

    private function createProcess(
        string $theMostRecentTag,
        string $localSubdirectory,
        string $remoteRepository
    ): Process {
        $this->repositoryGuard->ensureIsRepository($remoteRepository);

        return new Process(sprintf(
            'git subsplit publish --heads=master %s %s',
            $this->hasRepositoryTag(
                $remoteRepository,
                $theMostRecentTag
            ) ? '' : sprintf('--tags=%s', $theMostRecentTag),
            sprintf('%s:%s', $localSubdirectory, $remoteRepository)
        ));
    }

    private function hasRepositoryTag(string $remoteRepository, string $theMostRecentTag): bool
    {
        $process = new Process(sprintf('git ls-remote %s refs/tags/%s', $remoteRepository, $theMostRecentTag));
        $process->run();

        return (bool) $process->getOutput();
    }
}
