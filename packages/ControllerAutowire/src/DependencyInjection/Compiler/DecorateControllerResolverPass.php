<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symplify\ControllerAutowire\HttpKernel\Controller\ControllerResolver;

final class DecorateControllerResolverPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private const DEFAULT_CONTROLLER_RESOLVER_SERVICE_NAME = 'controller_resolver';

    public function process(ContainerBuilder $containerBuilder): void
    {
        $decoratedControllerResolverServiceName = $this->getCurrentControllerResolverServiceName($containerBuilder);

        $definition = new Definition(ControllerResolver::class, [
            new Reference(ControllerResolver::class . '.inner'),
            new Reference('service_container'),
            new Reference('controller_name_converter'),
        ]);

        $definition->setDecoratedService($decoratedControllerResolverServiceName, null, 1);
        $definition->setAutowiringTypes([ControllerResolverInterface::class]);

        $containerBuilder->setDefinition(ControllerResolver::class, $definition);
    }

    private function getCurrentControllerResolverServiceName(ContainerBuilder $containerBuilder): string
    {
        if ($containerBuilder->has('debug.' . self::DEFAULT_CONTROLLER_RESOLVER_SERVICE_NAME)) {
            return 'debug.' . self::DEFAULT_CONTROLLER_RESOLVER_SERVICE_NAME;
        }

        return self::DEFAULT_CONTROLLER_RESOLVER_SERVICE_NAME;
    }
}
