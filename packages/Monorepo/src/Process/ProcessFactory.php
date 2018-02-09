<?php declare(strict_types=1);

namespace Symplify\Monorepo\Process;

use Symfony\Component\Process\Process;
use Symplify\Monorepo\Configuration\BashFiles;
use Symplify\Monorepo\Configuration\RepositoryGuard;

final class ProcessFactory
{
    /**
     * @var string
     */
    private $cwd;

    /**
     * @var RepositoryGuard
     */
    private $repositoryGuard;

    public function __construct(RepositoryGuard $repositoryGuard)
    {
        $this->repositoryGuard = $repositoryGuard;
    }

    public function setCurrentWorkingDirectory(string $cwd): void
    {
        $this->cwd = $cwd;
    }

    public function createSubsplitInit(): Process
    {
        return new Process([realpath(BashFiles::SUBSPLIT), 'init', '.git'], $this->cwd);
    }

    public function createSubsplitPublish(
        string $theMostRecentTag,
        string $localSubdirectory,
        string $remoteRepository
    ): Process {
        $this->repositoryGuard->ensureIsRepository($remoteRepository);

        $commandLine = [
            realpath(BashFiles::SUBSPLIT),
            'publish',
            '--heads=master',
            $theMostRecentTag ? sprintf('--tags=%s', $theMostRecentTag) : '',
            $localSubdirectory . ':' . $remoteRepository,
        ];

        return new Process($commandLine, $this->cwd, null, null, null);
    }
}
