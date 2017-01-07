<?php

declare(strict_types = 1);

namespace Symplify\ServiceDefinitionDecorator\Adapter\Symfony\DependencyInjection\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symplify\ServiceDefinitionDecorator\Adapter\Symfony\SymplifyServiceDefinitionDecoratorBundle;

final class ContainerExtension extends Extension
{
    public function getAlias() : string
    {
        return SymplifyServiceDefinitionDecoratorBundle::ALIAS;
    }

    public function load(array $configs, ContainerBuilder $containerBuilder)
    {
    }
}
