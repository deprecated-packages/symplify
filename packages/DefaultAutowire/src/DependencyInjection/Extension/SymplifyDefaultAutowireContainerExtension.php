<?php

declare(strict_types=1);

namespace Symplify\DefaultAutowire\DependencyInjection\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symplify\DefaultAutowire\SymplifyDefaultAutowireBundle;

final class SymplifyDefaultAutowireContainerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function getAlias() : string
    {
        return SymplifyDefaultAutowireBundle::ALIAS;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $containerBuilder)
    {
    }
}
