<?php

declare(strict_types = 1);

/**
 * This file is part of Symplify.
 * Copyright (c) 2017 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ServiceDefinitionDecorator\Adapter\Symfony;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\ServiceDefinitionDecorator\Adapter\Symfony\DependencyInjection\Compiler\DecorateCompilerPass;
use Symplify\ServiceDefinitionDecorator\Adapter\Symfony\DependencyInjection\Extension\ContainerExtension;

final class SymplifyServiceDefinitionDecoratorBundle extends Bundle
{
    /**
     * @var string
     */
    const ALIAS = 'decorator';

    public function build(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new DecorateCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 20);
    }

    public function getContainerExtension() : ContainerExtension
    {
        return new ContainerExtension();
    }
}
