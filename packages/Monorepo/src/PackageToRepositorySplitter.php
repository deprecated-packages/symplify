<?php declare(strict_types=1);

namespace Symplify\Monorepo;

use GitWrapper\GitWorkingCopy;
use Spatie\Async\Pool;
use Spatie\Async\Process\ParallelProcess;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Symplify\Monorepo\Configuration\RepositoryGuard;

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

        $pool = Pool::create();

        $i = 0;
        foreach ($splitConfig as $localSubdirectory => $remoteRepository) {
            $process = $this->createProcess($theMostRecentTag, $localSubdirectory, $remoteRepository);
            $parallelProcess = ParallelProcess::create($process, ++$i);

            $pool->add($parallelProcess)
                ->then(function () use ($localSubdirectory, $remoteRepository): void {
                    $this->symfonyStyle->success(sprintf(
                        'Package "%s" was split to "%s" repository',
                        $localSubdirectory,
                        $remoteRepository
                    ));
                });
        }

        $pool->wait();
    }

    private function getMostRecentTag(GitWorkingCopy $gitWorkingCopy): string
    {
        $tags = $gitWorkingCopy->tag('-l');
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
            sprintf('--tags=%s', $theMostRecentTag),
            sprintf('%s:%s', $localSubdirectory, $remoteRepository)
        ));
    }
}
