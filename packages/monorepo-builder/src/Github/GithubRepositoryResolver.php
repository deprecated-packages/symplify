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
        return $this->resolveGitHubRepositoryName($repositoryOriginUrl);
    }

    public function resolveGitHubRepositoryName(string $repositoryOriginUrl): string
    {
        $repositoryUrl = $this->githubRepositoryFromRemoteResolver->resolveFromUrl($repositoryOriginUrl);
        if (! Strings::startsWith($repositoryUrl, self::GITHUB_URL)) {
            throw new ShouldNotHappenException(
                sprintf(
                    'Remote URL "%s" is not hosted on GitHub (should start with "%s")',
                    $repositoryUrl,
                    self::GITHUB_URL
                )
            );
        }
        // Extract the account name: everything after "github.com/", and before the next "/"
        $repository = Strings::substring($repositoryUrl, Strings::length(self::GITHUB_URL));
        return Strings::before($repository, '/');
    }
}
