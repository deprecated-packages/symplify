<?php

declare(strict_types = 1);

/**
 * This file is part of Symplify.
 * Copyright (c) 2017 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ServiceDefinitionDecorator\Adapter\Symfony\DependencyInjection\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symplify\ServiceDefinitionDecorator\Adapter\Symfony\SymplifyServiceDefinitionDecoratorBundle;

final class ContainerExtension extends Extension
{
    public function getAlias() : string
    {
        return SymplifyServiceDefinitionDecoratorBundle::ALIAS;
    }

    public function load(array $configs, ContainerBuilder $containerBuilder)
    {
    }
}
