<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Adapter\Symfony\DependencyInjection\Extension;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class ModularDoctrineFiltersExtension extends Extension
{
    /**
     * @param array[] $configs
     * @param ContainerBuilder $containerBuilder
     */
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        (new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../../../../config')))
            ->load('services.neon');
    }
}
