<?php declare(strict_types=1);

namespace Symplify\Monorepo;

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

            // local hash...:
            $localSubdirectoryLastCommitHash = $this->localSubdirectoryLastCommitHash($localSubdirectory);
            dump($localSubdirectory);
            dump($localSubdirectoryLastCommitHash);
            # todo: validate with remote
            # https://stackoverflow.com/questions/27611995/removing-invalid-git-subtree-split-hash
            # https://www.google.cz/search?ei=UUpzWu24NYLyUNv-ibAH&q=validate+subtree+split+commit+hash&oq=validate+subtree+split+commit+hash&gs_l=psy-ab.3..33i160k1.6870.7304.0.7440.5.4.0.0.0.0.106.347.3j1.4.0.crnk_dmh...0...1.1.64.psy-ab..1.3.240...33i21k1.0.8ajFE3MF8mk
            die;

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

    private function getMostRecentTag(): string
    {
        $process = new Process('git tag -l --sort=committerdate');
        $process->run();
        $tags = $process->getOutput();
        $tagList = explode(PHP_EOL, trim($tags));

        return (string) array_pop($tagList);
    }

    private function createProcess(
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

    private function localSubdirectoryLastCommitHash(string $localSubdirectory): string
    {
        $process = new Process(sprintf('git log -n 1 --pretty=format:"%%H" %s', $localSubdirectory));
        $process->run();

        return trim($process->getOutput());
    }
}
