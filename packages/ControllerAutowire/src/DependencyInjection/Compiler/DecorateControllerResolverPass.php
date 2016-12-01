<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2015 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ControllerAutowire\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symplify\ControllerAutowire\Contract\DependencyInjection\ControllerClassMapInterface;
use Symplify\ControllerAutowire\HttpKernel\Controller\ControllerResolver;

final class DecorateControllerResolverPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    const DEFAULT_CONTROLLER_RESOLVER_SERVICE_NAME = 'controller_resolver';

    /**
     * @var string
     */
    const SYMPLIFY_CONTROLLER_RESOLVER_SERVICE_NAME = 'symplify.controller_resolver';

    /**
     * @var ControllerClassMapInterface
     */
    private $controllerClassMap;

    public function __construct(ControllerClassMapInterface $controllerClassMap)
    {
        $this->controllerClassMap = $controllerClassMap;
    }

    public function process(ContainerBuilder $containerBuilder)
    {
        $decoratedControllerResolverServiceName = $this->getCurrentControllerResolverServiceName($containerBuilder);

        $definition = new Definition(ControllerResolver::class, [
            new Reference(self::SYMPLIFY_CONTROLLER_RESOLVER_SERVICE_NAME . '.inner'),
            new Reference('service_container'),
            new Reference('controller_name_converter'),
        ]);

        $definition->setDecoratedService($decoratedControllerResolverServiceName, null, 1);
        $definition->addMethodCall('setControllerClassMap', [$this->controllerClassMap->getControllers()]);
        $definition->setAutowiringTypes([ControllerResolverInterface::class]);

        $containerBuilder->setDefinition(self::SYMPLIFY_CONTROLLER_RESOLVER_SERVICE_NAME, $definition);
    }

    private function getCurrentControllerResolverServiceName(ContainerBuilder $containerBuilder) : string
    {
        if ($containerBuilder->has('debug.' . self::DEFAULT_CONTROLLER_RESOLVER_SERVICE_NAME)) {
            return 'debug.' . self::DEFAULT_CONTROLLER_RESOLVER_SERVICE_NAME;
        }

        return self::DEFAULT_CONTROLLER_RESOLVER_SERVICE_NAME;
    }
}
