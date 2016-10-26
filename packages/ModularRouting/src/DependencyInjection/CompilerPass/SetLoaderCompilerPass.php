<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ModularRouting\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symplify\ModularRouting\Routing\AbstractRouteCollectionProvider;

final class SetLoaderCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $containerBuilder)
    {
        foreach ($containerBuilder->getDefinitions() as $definition) {
            if (is_subclass_of($definition->getClass(), AbstractRouteCollectionProvider::class)) {
                $definition->addMethodCall('setLoaderResolver', [new Reference('routing.resolver')]);
            }
        }
    }
}
