<?php declare(strict_types=1);

namespace Symplify\Monorepo;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
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

        foreach ($splitConfig as $localSubdirectory => $remoteRepository) {
            if ($this->isRemoteUpToDate($localSubdirectory, $remoteRepository)) {
                continue;
            }

            $process = $this->createSubsplitPublishProcess($theMostRecentTag, $localSubdirectory, $remoteRepository);
            $process->run();

            if ($process->isSuccessful()) {
                $this->symfonyStyle->success(trim($process->getOutput()));
            } else {
                throw new PackageToRepositorySplitException($process->getErrorOutput());
            }
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
