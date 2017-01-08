<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\DependencyInjection\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symplify\DefaultAutowire\SymplifyDefaultAutowireBundle;

final class SymplifyDefaultAutowireContainerExtension extends Extension
{
    public function getAlias() : string
    {
        return SymplifyDefaultAutowireBundle::ALIAS;
    }

    public function load(array $configs, ContainerBuilder $containerBuilder)
    {
    }
}
