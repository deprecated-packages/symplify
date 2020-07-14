<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator\DependencyInjection\Extension;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class EasyHydratorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        $loader = new PhpFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../../../config'));
        $loader->load('config.php');
    }
}
