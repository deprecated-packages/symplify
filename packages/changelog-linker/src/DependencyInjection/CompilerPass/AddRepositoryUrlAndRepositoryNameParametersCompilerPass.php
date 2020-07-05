<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\DependencyInjection\CompilerPass;

use Nette\Utils\Strings;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Process\Process;
use Symplify\ChangelogLinker\Github\GithubRepositoryFromRemoteResolver;

final class AddRepositoryUrlAndRepositoryNameParametersCompilerPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private const OPTION_REPOSITORY_NAME = 'repository_name';

    /**
     * @var string
     */
    private const OPTION_REPOSITORY_URL = 'repository_url';

    /**
     * @var GithubRepositoryFromRemoteResolver
     */
    private $githubRepositoryFromRemoteResolver;

    public function __construct()
    {
        $this->githubRepositoryFromRemoteResolver = new GithubRepositoryFromRemoteResolver();
    }

    public function process(ContainerBuilder $containerBuilder): void
    {
        if (! $containerBuilder->hasParameter(self::OPTION_REPOSITORY_URL)) {
            $containerBuilder->setParameter(self::OPTION_REPOSITORY_URL, $this->detectRepositoryUrlFromGit());
        }

        if (! $containerBuilder->hasParameter(self::OPTION_REPOSITORY_NAME)) {
            $containerBuilder->setParameter(
                self::OPTION_REPOSITORY_NAME,
                $this->detectRepositoryName($containerBuilder)
            );
        }
    }

    private function detectRepositoryUrlFromGit(): string
    {
        $process = new Process(['git', 'config', '--get', 'remote.origin.url']);
        $process->run();

        $trimmedOutput = trim($process->getOutput());
        return $this->githubRepositoryFromRemoteResolver->resolveFromUrl($trimmedOutput);
    }

    private function detectRepositoryName(ContainerBuilder $containerBuilder): string
    {
        $repositoryUrl = $containerBuilder->getParameter(self::OPTION_REPOSITORY_URL);

        return Strings::substring($repositoryUrl, Strings::length('https://github.com/'));
    }
}
