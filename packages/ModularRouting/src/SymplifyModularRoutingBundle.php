<?php declare(strict_types=1);

namespace Symplify\ModularRouting;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\ModularRouting\DependencyInjection\CompilerPass\AddRouteCollectionProvidersCompilerPass;
use Symplify\ModularRouting\DependencyInjection\Extension\SymplifyModularRoutingExtension;

final class SymplifyModularRoutingBundle extends Bundle
{
    public function getContainerExtension(): SymplifyModularRoutingExtension
    {
        return new SymplifyModularRoutingExtension;
    }

    public function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new AddRouteCollectionProvidersCompilerPass);
    }
}
