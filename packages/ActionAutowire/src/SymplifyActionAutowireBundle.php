<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2015 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ActionAutowire;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\ActionAutowire\DependencyInjection\Compiler\AddServiceLocatorCompilerPass;
use Symplify\ActionAutowire\DependencyInjection\Compiler\ServicesByTypeMapCompilerPass;
use Symplify\ActionAutowire\DependencyInjection\Container\ServicesByTypeMap;
use Symplify\ActionAutowire\DependencyInjection\Extension\ContainerExtension;

final class SymplifyActionAutowireBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension() : ContainerExtension
    {
        return new ContainerExtension();
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $containerBuilder)
    {
        $serviceByTypeMap = new ServicesByTypeMap();
        $containerBuilder->addCompilerPass(new ServicesByTypeMapCompilerPass($serviceByTypeMap));
        $containerBuilder->addCompilerPass(new AddServiceLocatorCompilerPass($serviceByTypeMap));
    }
}
