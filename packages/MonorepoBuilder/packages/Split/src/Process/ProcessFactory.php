<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Process;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Process\Process;
use Symplify\MonorepoBuilder\Split\Configuration\RepositoryGuard;
use function Safe\realpath;
use function Safe\sprintf;

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

    /**
     * @var string
     */
    private $repository;

    public function __construct(
        RepositoryGuard $repositoryGuard,
        string $subsplitCacheDirectory,
        string $repository
    ) {
        $this->repositoryGuard = $repositoryGuard;
        $this->subsplitCacheDirectory = $subsplitCacheDirectory;
        $this->repository = $repository;
    }

    public function createSubsplit(
        string $theMostRecentTag,
        string $directory,
        string $remoteRepository
    ): Process {
        $this->repositoryGuard->ensureIsRepository($remoteRepository);

        $commandLine = [
            realpath(self::SUBSPLIT_BASH_FILE),
            sprintf('--from-directory=%s', $directory),
            sprintf('--to-repository=%s', $remoteRepository),
            '--branch=master',
            $theMostRecentTag ? sprintf('--tag=%s', $theMostRecentTag) : '',
            sprintf('--repository=%s', $this->repository),
        ];

        return $this->createProcessFromCommandLine($commandLine, $directory);
    }

    /**
     * @param mixed[] $commandLine
     */
    private function createProcessFromCommandLine(array $commandLine, string $directory): Process
    {
        $directory = $this->subsplitCacheDirectory . DIRECTORY_SEPARATOR . Strings::webalize($directory);

        FileSystem::delete($directory);
        FileSystem::createDir($directory);

        return new Process($commandLine, $directory, null, null, null);
    }
}
