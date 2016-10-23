<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2015 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ControllerAutowire\Config\Definition;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\ControllerAutowire\Contract\Config\Definition\ConfigurationResolverInterface;
use Symplify\ControllerAutowire\SymplifyControllerAutowireBundle;

final class ConfigurationResolver implements ConfigurationResolverInterface
{
    /**
     * @var string[]
     */
    private $resolvedConfiguration;

    public function resolveFromContainerBuilder(ContainerBuilder $containerBuilder) : array
    {
        if (! $this->resolvedConfiguration) {
            $processor = new Processor();
            $configs = $containerBuilder->getExtensionConfig(SymplifyControllerAutowireBundle::ALIAS);
            $configs = $processor->processConfiguration(new Configuration(), $configs);

            $this->resolvedConfiguration = $containerBuilder->getParameterBag()->resolveValue($configs);
        }

        return $this->resolvedConfiguration;
    }
}
