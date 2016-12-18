<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ModularDoctrineFilters\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symplify\ModularDoctrineFilters\Contract\Filter\FilterInterface;

final class LoadFiltersCompilerPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    const NAME_CONFIGURATION = 'doctrine.orm.default_configuration';

    /**
     * @var string
     */
    const NAME_CONFIGURATOR = 'doctrine.orm.default_manager_configurator';

    /**
     * @var string[]
     */
    private $newFilters = [];

    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $containerBuilder)
    {
        if (! $containerBuilder->hasDefinition(self::NAME_CONFIGURATION)) {
            return;
        }

        $this->containerBuilder = $containerBuilder;

        $this->addFiltersToOrmConfiguration();
    }

    private function addFiltersToOrmConfiguration()
    {
        $defaultOrmConfiguration = $this->containerBuilder->getDefinition(self::NAME_CONFIGURATION);
        $filterManager = $this->containerBuilder->getDefinition('symplify.filter_manager');

        foreach ($this->getAllFilters() as $name => $definition) {
            // 1) load to Doctrine
            $defaultOrmConfiguration->addMethodCall('addFilter', [$name, $definition->getClass()]);
            $this->newFilters[] = $name;

            // 2) load to FilterManager to run conditions and enable allowed only
            $filterManager->addMethodCall('addFilter', [$name, new Reference($name)]);
        }

        $this->passFilterManagerToListener();
    }

    /**
     * Prevents circular reference.
     */
    private function passFilterManagerToListener()
    {
        $enableFiltersSubscriber = $this->containerBuilder->getDefinition('symplify.enable_filters_listener');
        $enableFiltersSubscriber->addMethodCall('setFilterManager', [new Reference('symplify.filter_manager')]);
    }

    /**
     * @return Definition[]
     */
    private function getAllFilters() : array
    {
        $filters = [];
        foreach ($this->containerBuilder->getDefinitions() as $name => $definition) {
            if (is_subclass_of($definition->getClass(), FilterInterface::class)) {
                $filters[$name] = $definition;
            }
        }

        return $filters;
    }
}
