<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Process;

use Nette\Utils\Strings;
use Symfony\Component\Process\Process;
use Symplify\MonorepoBuilder\Split\Configuration\RepositoryGuard;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SmartFileSystem\SmartFileSystem;

/**
 * @see \Symplify\MonorepoBuilder\Split\Tests\Process\ProcessFactoryTest
 */
final class ProcessFactory
{
    /**
     * @var string
     */
    private const SUBSPLIT_BASH_FILE = __DIR__ . '/../../bash/subsplit.sh';

    /**
     * @var string
     */
    private $repository;

    /**
     * @var string
     */
    private $subsplitCacheDirectory;

    /**
     * @var RepositoryGuard
     */
    private $repositoryGuard;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct(
        RepositoryGuard $repositoryGuard,
        SmartFileSystem $smartFileSystem,
        ParameterProvider $parameterProvider
    ) {
        $this->repositoryGuard = $repositoryGuard;
        $this->subsplitCacheDirectory = $parameterProvider->provideStringParameter(Option::SUBSPLIT_CACHE_DIRECTORY);
        $this->repository = $parameterProvider->provideStringParameter(Option::REPOSITORY);
        $this->smartFileSystem = $smartFileSystem;
    }

    public function createSubsplit(
        ?string $theMostRecentTag,
        string $directory,
        string $remoteRepository,
        string $branch
    ): Process {
        $this->repositoryGuard->ensureIsRepository($remoteRepository);

        $commandLine = [
            realpath(self::SUBSPLIT_BASH_FILE),
            sprintf('--from-directory=%s', $directory),
            sprintf('--to-repository=%s', $remoteRepository),
            sprintf('--branch=%s', $branch),
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

        $this->smartFileSystem->remove($directory);
        $this->smartFileSystem->mkdir($directory);

        return new Process($commandLine, $directory, null, null, null);
    }
}
