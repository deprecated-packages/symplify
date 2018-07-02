<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Process;

use Symfony\Component\Process\Process;
use Symplify\MonorepoBuilder\Split\Configuration\RepositoryGuard;

final class ProcessFactory
{
    /**
     * @var string
     */
    private const SUBSPLIT_BASH_FILE = __DIR__ . '/../../bash/subsplit.sh';

    /**
     * @var RepositoryGuard
     */
    private $repositoryGuard;

    /**
     * @var string
     */
    private $rootDirectory;

    public function __construct(RepositoryGuard $repositoryGuard, string $rootDirectory)
    {
        $this->repositoryGuard = $repositoryGuard;
        $this->rootDirectory = $rootDirectory;
    }

    public function createSubsplit(
        string $subsplitDirectory,
        string $theMostRecentTag,
        string $directory,
        string $remoteRepository,
        bool $isVerbose
    ): Process {
        $this->repositoryGuard->ensureIsRepository($remoteRepository);

        $commandLine = [
            realpath(self::SUBSPLIT_BASH_FILE),
            sprintf('--work-dir=%s', $subsplitDirectory),
            '--branches=master',
            $theMostRecentTag ? sprintf('--tags=%s', $theMostRecentTag) : '',
            $directory . ':' . $remoteRepository,
            $isVerbose ? '--debug' : '',
        ];

        return $this->createProcessFromCommandLine($commandLine, $subsplitDirectory);
    }

    /**
     * @param mixed[] $commandLine
     */
    private function createProcessFromCommandLine(array $commandLine, string $subsplitDirectory): Process
    {
        return new Process($commandLine, $subsplitDirectory, null, null, null);
    }
}
