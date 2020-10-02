<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\ChangelogLinker\DependencyInjection\CompilerPass\AddRepositoryUrlAndRepositoryNameParametersCompilerPass;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class ChangelogLinkerKernel extends AbstractSymplifyKernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');

        parent::registerContainerConfiguration($loader);
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new AddRepositoryUrlAndRepositoryNameParametersCompilerPass());
    }
}
