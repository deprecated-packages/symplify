<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ModularRouting;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\ModularRouting\DependencyInjection\CompilerPass\AddRouteCollectionProvidersCompilerPass;
use Symplify\ModularRouting\DependencyInjection\CompilerPass\SetLoaderCompilerPass;
use Symplify\ModularRouting\DependencyInjection\Extension\SymplifyModularRoutingExtension;

final class SymplifyModularRoutingBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new SymplifyModularRoutingExtension();
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new AddRouteCollectionProvidersCompilerPass());
        $containerBuilder->addCompilerPass(new SetLoaderCompilerPass());
    }
}
