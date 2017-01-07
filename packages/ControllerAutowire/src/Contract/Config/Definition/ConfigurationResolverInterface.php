<?php

declare(strict_types=1);

namespace Symplify\ControllerAutowire\Contract\Config\Definition;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ConfigurationResolverInterface
{
    /**
     * @return string[][]
     */
    public function resolveFromContainerBuilder(ContainerBuilder $containerBuilder);
}
