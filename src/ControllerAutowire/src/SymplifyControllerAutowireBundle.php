<?php

declare (strict_types = 1);

/*
 * This file is part of Symplify
 * Copyright (c) 2015 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ControllerAutowire;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\ControllerAutowire\DependencyInjection\Compiler\RegisterControllersPass;
use Symplify\ControllerAutowire\DependencyInjection\Compiler\ReplaceControllerResolverPass;
use Symplify\ControllerAutowire\DependencyInjection\ControllerClassMap;
use Symplify\ControllerAutowire\DependencyInjection\Extension\ContainerExtension;
use Symplify\ControllerAutowire\HttpKernel\Controller\ControllerFinder;

final class SymplifyControllerAutowireBundle extends Bundle
{
    /**
     * @var string
     */
    const ALIAS = 'symplify_controller_autowire';

    public function build(ContainerBuilder $container)
    {
        $controllerClassMap = new ControllerClassMap();
        $controllerFinder = new ControllerFinder();

        $container->addCompilerPass(new RegisterControllersPass($controllerClassMap, $controllerFinder));
        $container->addCompilerPass(new ReplaceControllerResolverPass($controllerClassMap), PassConfig::TYPE_OPTIMIZE);
    }

    public function createContainerExtension() : ContainerExtension
    {
        return new ContainerExtension();
    }
}
