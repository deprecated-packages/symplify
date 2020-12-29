<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Github;

use Nette\Utils\Strings;
use Symfony\Component\Process\Process;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

/**
 * @see \Symplify\MonorepoBuilder\Tests\Github\GithubRepositoryResolverTest
 */
final class GithubRepositoryResolver
{
    /**
     * @var string
     */
    private const GITHUB_URL = 'https://github.com/';

    /**
     * @var string
     */
    private $repositoryOriginUrl = '';

    /**
     * @var array<string, string>
     */
    private $repositoryOwners = [];

    /**
     * @var GithubRepositoryFromRemoteResolver
     */
    private $githubRepositoryFromRemoteResolver;

    public function __construct(GithubRepositoryFromRemoteResolver $githubRepositoryFromRemoteResolver)
    {
        $this->githubRepositoryFromRemoteResolver = $githubRepositoryFromRemoteResolver;
    }

    public function resolveGitHubRepositoryOwnerFromRemote(): string
    {
        $repositoryOriginUrl = $this->resolveGitHubRepositoryOriginUrl();
        return $this->resolveGitHubRepositoryOwner($repositoryOriginUrl);
    }

    public function resolveGitHubRepositoryOriginUrl(): string
    {
        if ($this->repositoryOriginUrl === '') {
            $process = new Process(['git', 'config', '--get', 'remote.origin.url']);
            $process->run();
            $this->repositoryOriginUrl = trim($process->getOutput());
        }
        return $this->repositoryOriginUrl;
    }

    public function resolveGitHubRepositoryOwner(string $repositoryOriginUrl): string
    {
        if (isset($this->repositoryOwners[$repositoryOriginUrl])) {
            return $this->repositoryOwners[$repositoryOriginUrl];
        }

        // Extract the owner: everything after "github.com/", and before the next "/"
        $repositoryUrl = $this->githubRepositoryFromRemoteResolver->resolveFromUrl($repositoryOriginUrl);

        // Make sure the format is https://github.com/owner/package
        if (! Strings::startsWith($repositoryUrl, self::GITHUB_URL)) {
            throw new ShouldNotHappenException(
                sprintf(
                    'Remote URL "%s" is not hosted on GitHub (should start with "%s")',
                    $repositoryUrl,
                    self::GITHUB_URL
                )
            );
        }
        $repository = Strings::substring($repositoryUrl, Strings::length(self::GITHUB_URL));
        $owner = Strings::before($repository, '/');
        if ($owner === null) {
            throw new ShouldNotHappenException(
                sprintf(
                    'Cannot deduce the owner for remote URL "%s"',
                    $repositoryUrl
                )
            );
        }
        $this->repositoryOwners[$repositoryOriginUrl] = $owner;
        return $this->repositoryOwners[$repositoryOriginUrl];
    }
}
