<?php declare(strict_types=1);

namespace Symplify\ModularRouting\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\ModularRouting\Contract\Routing\ModularRouterInterface;
use Symplify\ModularRouting\Contract\Routing\RouteCollectionProviderInterface;
use Symplify\PackageBuilder\Adapter\Symfony\DependencyInjection\DefinitionCollector;

final class AddRouteCollectionProvidersCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            ModularRouterInterface::class,
            RouteCollectionProviderInterface::class,
            'addRouteCollectionProvider'
        );
    }
}
