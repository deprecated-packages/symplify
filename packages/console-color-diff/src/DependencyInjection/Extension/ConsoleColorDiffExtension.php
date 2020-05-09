<?php

declare(strict_types=1);

namespace Symplify\ConsoleColorDiff\DependencyInjection\Extension;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class ConsoleColorDiffExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        $yamlFileLoader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../../../config'));

        $yamlFileLoader->load('config.yaml');
    }
}
