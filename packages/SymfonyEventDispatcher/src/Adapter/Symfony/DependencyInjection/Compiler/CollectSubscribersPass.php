<?php

declare(strict_types = 1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\SymfonyEventDispatcher\Adapter\Symfony\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CollectSubscribersPass implements CompilerPassInterface
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    public function process(ContainerBuilder $containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;

        $this->loadSubscribersToEventDispatcher();
    }

    private function loadSubscribersToEventDispatcher()
    {
        $eventDispatcherDefinition = $this->containerBuilder->findDefinition('symplify.event_dispatcher');
        foreach ($this->containerBuilder->getDefinitions() as $name => $definition) {
            if (! is_subclass_of($definition->getClass(), EventSubscriberInterface::class)) {
                return;
            }

            $eventDispatcherDefinition->addMethodCall('addSubscriber', [new Reference($name)]);
        }
    }
}
