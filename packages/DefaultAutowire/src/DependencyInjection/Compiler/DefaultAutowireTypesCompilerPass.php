<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\DefaultAutowire\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\DefaultAutowire\Config\Definition\ConfigurationResolver;

final class DefaultAutowireTypesCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $containerBuilder)
    {
        $autowireTypes = $this->getAutowireTypes($containerBuilder);
        foreach ($autowireTypes as $type => $serviceName) {
            if (! $containerBuilder->has($serviceName)) {
                continue;
            }

            if ($containerBuilder->hasAlias($serviceName)) {
                $serviceName = $containerBuilder->getAlias($serviceName);
            }

            $containerBuilder->getDefinition($serviceName)
                ->setAutowiringTypes([$type]);
        }
    }

    /**
     * @return string[]
     */
    private function getAutowireTypes(ContainerBuilder $containerBuilder) : array
    {
        $config = (new ConfigurationResolver())->resolveFromContainerBuilder($containerBuilder);

        return $config['autowire_types'];
    }
}
