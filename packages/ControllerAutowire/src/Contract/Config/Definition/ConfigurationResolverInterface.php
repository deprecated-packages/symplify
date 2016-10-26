<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2015 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ControllerAutowire\Contract\Config\Definition;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ConfigurationResolverInterface
{
    /**
     * @return string[][]
     */
    public function resolveFromContainerBuilder(ContainerBuilder $containerBuilder);
}
