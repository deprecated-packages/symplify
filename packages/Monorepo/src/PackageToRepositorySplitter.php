<?php declare(strict_types=1);

namespace Symplify\Monorepo;

use GitWrapper\GitWorkingCopy;
use Spatie\Async\Pool;
use Spatie\Async\Process\ParallelProcess;
use Spatie\Async\Process\SynchronousProcess;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Symfony\Component\Stopwatch\Stopwatch;
use Symplify\Monorepo\Configuration\RepositoryGuard;
use Throwable;

/**
 * @wip
 */
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
     * @todo needs works
     * @param mixed[] $splitConfig
     */
    public function splitDirectoriesToRepositories(GitWorkingCopy $gitWorkingCopy, array $splitConfig): void
    {
        $theMostRecentTag = $this->getMostRecentTag($gitWorkingCopy);

        $stopwatch = new Stopwatch();
        $stopwatch->start('one');

        $pool = Pool::create();

        $i = 0;
        foreach ($splitConfig as $localSubdirectory => $remoteRepository) {
            $process = $this->createProcess($theMostRecentTag, $localSubdirectory, $remoteRepository);

            $parallelProcess = ParallelProcess::create($process, ++$i);

            // error
            $parallelProcess->catch(function (Throwable $throwable) {
                // @todo: false positive - often success
                $this->symfonyStyle->error($throwable->getMessage());
            });

            // success
            $parallelProcess->then(function ($output) use ($localSubdirectory, $remoteRepository): void {
                $this->symfonyStyle->success($output);
//                $this->symfonyStyle->success(sprintf(
//                    'Package "%s" was split to "%s" repository',
//                    $localSubdirectory,
//                    $remoteRepository
//                ));
            });

            $pool->add($parallelProcess);
        }

        $pool->wait();

        $time = $stopwatch->stop('one');
        $this->symfonyStyle->comment($time);
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
            $this->hasRepositoryTag($remoteRepository, $theMostRecentTag) ? '' : sprintf('--tags=%s', $theMostRecentTag),
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
