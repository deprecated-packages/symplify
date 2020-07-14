<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\DependencyInjection\Extension;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class SymplifyCodingStandardExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        // needed for parameter shifting of sniff/fixer params
        $checkerTolerantYamlFileLoader = new PhpFileLoader($containerBuilder, new FileLocator(
            __DIR__ . '/../../../config'
        ));
        $checkerTolerantYamlFileLoader->load('config.php');
    }
}
