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
    private const OPTION_REPOSITORY_URL = 'repository_url';

    public function process(ContainerBuilder $containerBuilder): void
    {
        // repository_url - probably this one is enough?
        if (! $containerBuilder->hasParameter(self::OPTION_REPOSITORY_URL)) {
            $containerBuilder->setParameter(self::OPTION_REPOSITORY_URL, $this->detectRepositoryUrlFromGit());
        }
    }

    private function detectRepositoryUrlFromGit(): ?string
    {
        $process = new Process('git config --get remote.origin.url');
        $process->run();

        $githubUrl = trim($process->getOutput());
        $githubUrl = rtrim($githubUrl, '.git');
        $githubUrl = str_replace(':', '/', $githubUrl);
        $githubUrl = substr($githubUrl, strlen('git@'));

        return 'https://' . $githubUrl;
    }
}
