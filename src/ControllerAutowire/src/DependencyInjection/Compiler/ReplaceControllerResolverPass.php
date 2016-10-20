<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2015 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ControllerAutowire\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symplify\ControllerAutowire\Contract\DependencyInjection\ControllerClassMapInterface;
use Symplify\ControllerAutowire\HttpKernel\Controller\ControllerResolver;

final class ReplaceControllerResolverPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    const CONTROLLER_RESOLVER_SERVICE_NAME = 'controller_resolver';

    /**
     * @var ControllerClassMapInterface
     */
    private $controllerClassMap;

    /**
     * @var bool
     */
    private $isControllerResolverAliased = false;

    public function __construct(ControllerClassMapInterface $controllerClassMap)
    {
        $this->controllerClassMap = $controllerClassMap;
    }

    public function process(ContainerBuilder $containerBuilder)
    {
        $controllerResolverServiceName = $this->getCurrentControllerResolverServiceName($containerBuilder);

        if ($this->isControllerResolverAliased) {
            $definition = $this->createDefinitionWithDecoratingResolver($controllerResolverServiceName);

            $containerBuilder->setDefinition('default.controller_resolver', $definition);
        } else {
            $oldResolver = $containerBuilder->getDefinition($controllerResolverServiceName);
            $containerBuilder->setDefinition('old.'.$controllerResolverServiceName, $oldResolver);
            $containerBuilder->removeDefinition($controllerResolverServiceName);

            $definition = $this->createDefinitionWithDecoratingResolver('old.'.$controllerResolverServiceName);

            $containerBuilder->setDefinition('symplify.autowire_controller_controller_resolver', $definition);
            $containerBuilder->setAlias(
                $controllerResolverServiceName,
                new Alias('symplify.autowire_controller_controller_resolver', true)
            );
        }
    }

    private function getCurrentControllerResolverServiceName(ContainerBuilder $containerBuilder) : string
    {
        if ($containerBuilder->hasAlias(self::CONTROLLER_RESOLVER_SERVICE_NAME)) {
            $this->isControllerResolverAliased = true;
            $alias = $containerBuilder->getAlias(self::CONTROLLER_RESOLVER_SERVICE_NAME);

            return (string) $alias;
        }

        return self::CONTROLLER_RESOLVER_SERVICE_NAME;
    }

    private function createDefinitionWithDecoratingResolver(string $controllerResolverServiceName) : Definition
    {
        $definition = new Definition(ControllerResolver::class, [
            new Reference($controllerResolverServiceName),
            new Reference('service_container'),
            new Reference('controller_name_converter'),
        ]);
        $definition->addMethodCall('setControllerClassMap', [$this->controllerClassMap->getControllers()]);

        return $definition;
    }
}
