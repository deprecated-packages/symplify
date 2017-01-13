<?php declare(strict_types = 1);

namespace Symplify\SymfonyEventDispatcher\Adapter\Symfony\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\PackageBuilder\Adapter\Symfony\DependencyInjection\DefinitionCollector;
use Symplify\PackageBuilder\Adapter\Symfony\DependencyInjection\DefinitionFinder;

final class CollectEventSubscribersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder) : void
    {
        $eventDispatcherDefinition = DefinitionFinder::getByType($containerBuilder, EventDispatcherInterface::class);
        if (is_a($eventDispatcherDefinition->getClass(), ContainerAwareEventDispatcher::class, true)) {
            $this->registerToContainerAwareEventDispatcher($containerBuilder, $eventDispatcherDefinition);

            return;
        }

        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            EventDispatcherInterface::class,
            EventSubscriberInterface::class,
            'addSubscriber'
        );
    }

    private function registerToContainerAwareEventDispatcher(
        ContainerBuilder $containerBuilder,
        Definition $eventDispatcherDefinition
    ) : void {
        $eventSubscriberDefinitions = DefinitionFinder::findAllByType($containerBuilder, EventSubscriberInterface::class);
        foreach ($eventSubscriberDefinitions as $name => $definition) {
            $eventDispatcherDefinition->addMethodCall(
                'addSubscriberService',
                [$name, $definition->getClass()]
            );
        }
    }
}
