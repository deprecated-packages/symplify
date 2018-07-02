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
    private $subsplitCacheDirectory;

    public function __construct(RepositoryGuard $repositoryGuard, string $subsplitCacheDirectory)
    {
        $this->repositoryGuard = $repositoryGuard;
        $this->subsplitCacheDirectory = $subsplitCacheDirectory;
    }

    public function createSubsplit(
        string $theMostRecentTag,
        string $directory,
        string $remoteRepository,
        bool $isVerbose
    ): Process {
        $this->repositoryGuard->ensureIsRepository($remoteRepository);

        $commandLine = [
            realpath(self::SUBSPLIT_BASH_FILE),
            '--branches=master',
            $theMostRecentTag ? sprintf('--tags=%s', $theMostRecentTag) : '',
            $directory . ':' . $remoteRepository,
            $isVerbose ? '--debug' : '',
        ];

        return $this->createProcessFromCommandLine($commandLine);
    }

    /**
     * @param mixed[] $commandLine
     */
    private function createProcessFromCommandLine(array $commandLine): Process
    {
        return new Process($commandLine, $this->subsplitCacheDirectory, null, null, null);
    }
}
