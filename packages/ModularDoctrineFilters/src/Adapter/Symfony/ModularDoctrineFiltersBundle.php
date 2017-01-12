<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Adapter\Symfony;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\ModularDoctrineFilters\Adapter\Symfony\DependencyInjection\Compiler\LoadFiltersCompilerPass;
use Symplify\ModularDoctrineFilters\Adapter\Symfony\DependencyInjection\Extension\ModularDoctrineFiltersExtension;

final class ModularDoctrineFiltersBundle extends Bundle
{
    public function getContainerExtension() : ModularDoctrineFiltersExtension
    {
        return new ModularDoctrineFiltersExtension;
    }

    public function build(ContainerBuilder $containerBuilder) : void
    {
        $containerBuilder->addCompilerPass(new LoadFiltersCompilerPass);
    }
}
