<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Github;

use Nette\Utils\Strings;
use Symfony\Component\Process\Process;

/**
 * @see \Symplify\MonorepoBuilder\Tests\Github\GithubRepositoryResolverTest
 */
final class GithubRepositoryResolver
{
    /**
     * @var GithubRepositoryFromRemoteResolver
     */
    private $githubRepositoryFromRemoteResolver;

    public function __construct(GithubRepositoryFromRemoteResolver $githubRepositoryFromRemoteResolver)
    {
        $this->githubRepositoryFromRemoteResolver = $githubRepositoryFromRemoteResolver;
    }

    public function resolveGitHubRepositoryNameFromRemote(): string
    {
        // Get the remote origin URL, with format: https://github.com/account/package
        $process = new Process(['git', 'config', '--get', 'remote.origin.url']);
        $process->run();
        $repositoryOriginUrl = trim($process->getOutput());
        $repositoryUrl = $this->githubRepositoryFromRemoteResolver->resolveFromUrl($repositoryOriginUrl);
        return $this->resolveGitHubRepositoryName($repositoryUrl);
    }

    public function resolveGitHubRepositoryName(string $repositoryUrl): string
    {
        // Extract the account name: everything after "github.com/", and before the next "/"
        $repository = Strings::substring($repositoryUrl, Strings::length('https://github.com/'));
        return Strings::before($repository, '/');
    }
}
