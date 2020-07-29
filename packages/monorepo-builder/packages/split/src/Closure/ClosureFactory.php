<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Closure;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symplify\MonorepoBuilder\Split\Configuration\RepositoryGuard;
use Symplify\MonorepoBuilder\Split\Git\GitSubsplit;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class ClosureFactory
{
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
     * @var GitSubsplit
     */
    private $gitSubsplit;

    public function __construct(
        RepositoryGuard $repositoryGuard,
        GitSubsplit $gitSubsplit,
        ParameterProvider $parameterProvider
    ) {
        $this->repositoryGuard = $repositoryGuard;
        $this->gitSubsplit = $gitSubsplit;
        $this->subsplitCacheDirectory = $parameterProvider->provideStringParameter(Option::SUBSPLIT_CACHE_DIRECTORY);
        $this->repository = $parameterProvider->provideStringParameter(Option::REPOSITORY);
    }

    public function createSubsplit(
        ?string $theMostRecentTag,
        string $directory,
        string $remoteRepository,
        string $branch,
        bool $dryRun
    ): callable {
        $this->repositoryGuard->ensureIsRepository($remoteRepository);

        return $this->createClosure(
            $this->repository,
            $directory,
            $remoteRepository,
            $branch,
            $theMostRecentTag,
            $dryRun
        );
    }

    private function createClosure(
        string $repository,
        string $fromDir,
        string $toRepo,
        ?string $branch,
        ?string $tag,
        bool $dryRun
    ): callable {
        $workDir = $this->subsplitCacheDirectory . DIRECTORY_SEPARATOR . Strings::webalize($fromDir);

        return function () use ($workDir, $repository, $fromDir, $toRepo, $branch, $tag, $dryRun): void {
            FileSystem::delete($workDir);
            FileSystem::createDir($workDir);
            $this->gitSubsplit->subsplit($workDir, $repository, $fromDir, $toRepo, $branch, $tag, $dryRun);
        };
    }
}
