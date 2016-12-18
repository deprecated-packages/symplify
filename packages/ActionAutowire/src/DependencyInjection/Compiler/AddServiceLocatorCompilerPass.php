<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ActionAutowire\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\ActionAutowire\DependencyInjection\Container\ServicesByTypeMap;

final class AddServiceLocatorCompilerPass implements CompilerPassInterface
{
    /**
     * @var ServicesByTypeMap
     */
    private $servicesByTypeMap;

    public function __construct(ServicesByTypeMap $servicesByTypeMap)
    {
        $this->servicesByTypeMap = $servicesByTypeMap;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->getDefinition('symplify.action_autowire.service_locator')
            ->addMethodCall('setServiceByTypeMap', [$this->servicesByTypeMap->getMap()]);
    }
}
