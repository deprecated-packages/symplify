<?php

declare(strict_types=1);

namespace Symplify\ControllerAutowire\DependencyInjection\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symplify\ControllerAutowire\SymplifyControllerAutowireBundle;

final class ContainerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function getAlias() : string
    {
        return SymplifyControllerAutowireBundle::ALIAS;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $containerBuilder)
    {
    }
}
