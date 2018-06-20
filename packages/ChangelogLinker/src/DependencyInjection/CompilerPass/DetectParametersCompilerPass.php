<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Process\Process;

final class DetectParametersCompilerPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private const OPTION_REPOSITORY_NAME = 'repository_name';

    /**
     * @var string
     */
    private const OPTION_REPOSITORY_URL = 'repository_url';

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

    private function detectRepositoryName(ContainerBuilder $containerBuilder): ?string
    {
        $repositoryUrl = $containerBuilder->getParameter(self::OPTION_REPOSITORY_URL);

        return substr($repositoryUrl, strlen('https://github.com/'));
    }

    /**
     * From:
     * - git@github.com:Symplify/Symplify.git
     *
     * To:
     * - https://github.com/Symplify/Symplify
     */
    private function detectRepositoryUrlFromGit(): ?string
    {
        $process = new Process('git config --get remote.origin.url');
        $process->run();

        $githubSshUrl = trim($process->getOutput());

        $githubSshUrl = substr($githubSshUrl, 0, - strlen('.git'));
        $githubSshUrl = str_replace(':', '/', $githubSshUrl);
        $githubSshUrl = substr($githubSshUrl, strlen('git@'));
        return 'https://' . $githubSshUrl;
    }
}
