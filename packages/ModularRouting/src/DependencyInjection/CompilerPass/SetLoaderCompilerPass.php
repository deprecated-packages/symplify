<?php declare(strict_types=1);

namespace Symplify\ModularRouting\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symplify\ModularRouting\Routing\AbstractRouteCollectionProvider;

final class SetLoaderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        foreach ($containerBuilder->getDefinitions() as $definition) {
            if (is_subclass_of($definition->getClass(), AbstractRouteCollectionProvider::class)) {
                $definition->addMethodCall('setLoaderResolver', [new Reference('routing.resolver')]);
            }
        }
    }
}
