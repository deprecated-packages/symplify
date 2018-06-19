<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\DependencyInjection\CompilerPass;

use Nette\Utils\Json;
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

    public function process(ContainerBuilder $containerBuilder)
    {
        if (! $containerBuilder->hasParameter(self::OPTION_REPOSITORY_NAME)) {
            $containerBuilder->setParameter(self::OPTION_REPOSITORY_NAME, $this->detectRepositoryNameFromComposerJson());
        }

        // repository_url - probably this one is enough?
        if (! $containerBuilder->hasParameter(self::OPTION_REPOSITORY_URL)) {
            $containerBuilder->setParameter(self::OPTION_REPOSITORY_URL, $this->detectRepositoryUrlFromGit());
        }
    }

    private function detectRepositoryNameFromComposerJson(): ?string
    {
        $composerJsonFilePath = getcwd() . '/composer.json';
        if (! file_exists($composerJsonFilePath)) {
            return null;
        }

        $composerJsonContent = file_get_contents($composerJsonFilePath);
        $composerJson = Json::decode($composerJsonContent, Json::FORCE_ARRAY);

        return $composerJson['name'] ?? null;
    }

    private function detectRepositoryUrlFromGit(): ?string
    {
        $process = new Process('git config --get remote.origin.url');
        $process->run();

        $githubSshUrl = trim($process->getOutput());
        $githubSshUrl = rtrim($githubSshUrl, '.git');
        $githubSshUrl = str_replace(':', '/', $githubSshUrl);
        $githubSshUrl = substr($githubSshUrl, strlen('git@'));
        $githubSshUrl = 'https://' . $githubSshUrl;

        return $githubSshUrl;
    }
}
