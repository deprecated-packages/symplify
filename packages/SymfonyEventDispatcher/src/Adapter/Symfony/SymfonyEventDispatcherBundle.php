<?php declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2017 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\SymfonyEventDispatcher\Adapter\Symfony;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\SymfonyEventDispatcher\Adapter\Symfony\DependencyInjection\Compiler\CollectEventSubscribersPass;
use Symplify\SymfonyEventDispatcher\Adapter\Symfony\DependencyInjection\Extension\ContainerExtension;

final class SymfonyEventDispatcherBundle extends Bundle
{
    public function build(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new CollectEventSubscribersPass, PassConfig::TYPE_BEFORE_REMOVING);
    }

    public function getContainerExtension() : ContainerExtension
    {
        return new ContainerExtension;
    }
}
