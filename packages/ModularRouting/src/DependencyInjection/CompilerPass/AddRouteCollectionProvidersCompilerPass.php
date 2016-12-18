<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ModularRouting\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symplify\ModularRouting\Contract\Routing\RouteCollectionProviderInterface;

final class AddRouteCollectionProvidersCompilerPass implements CompilerPassInterface
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;

        $modularRouterDefinition = $containerBuilder->getDefinition('symplify.modular_routing.modular_router');
        foreach ($this->getAllRouteCollectionProviders() as $serviceId => $attributes) {
            $modularRouterDefinition->addMethodCall('addRouteCollectionProvider', [new Reference($serviceId)]);
        }
    }

    /**
     * @return RouteCollectionProviderInterface[]
     */
    private function getAllRouteCollectionProviders() : array
    {
        $filters = [];
        foreach ($this->containerBuilder->getDefinitions() as $name => $definition) {
            if (is_subclass_of($definition->getClass(), RouteCollectionProviderInterface::class)) {
                $filters[$name] = $definition;
            }
        }

        return $filters;
    }
}
