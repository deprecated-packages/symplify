<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\ModularDoctrineFilters\DependencyInjection\Compiler\LoadFiltersCompilerPass;
use Symplify\ModularDoctrineFilters\DependencyInjection\Extension\SymplifyModularDoctrineFiltersExtension;

final class SymplifyModularDoctrineFiltersBundle extends Bundle
{
    public function getContainerExtension() : SymplifyModularDoctrineFiltersExtension
    {
        return new SymplifyModularDoctrineFiltersExtension();
    }

    public function build(ContainerBuilder $containerBuilder) : void
    {
        $containerBuilder->addCompilerPass(new LoadFiltersCompilerPass());
    }
}
