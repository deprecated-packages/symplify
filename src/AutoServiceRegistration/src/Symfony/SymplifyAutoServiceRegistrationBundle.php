<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\AutoServiceRegistration\Symfony;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\AutoServiceRegistration\ServiceClass\ServiceClassFinder;
use Symplify\AutoServiceRegistration\Symfony\DependencyInjection\Compiler\AutoRegisterServicesCompilerPass;
use Symplify\AutoServiceRegistration\Symfony\DependencyInjection\Extension\ContainerExtension;

final class SymplifyAutoServiceRegistrationBundle extends Bundle
{
    /**
     * @var string
     */
    const ALIAS = 'symplify_auto_service_registration';

    public function build(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new AutoRegisterServicesCompilerPass(new ServiceClassFinder()));
    }

    public function createContainerExtension()
    {
        return new ContainerExtension();
    }
}
