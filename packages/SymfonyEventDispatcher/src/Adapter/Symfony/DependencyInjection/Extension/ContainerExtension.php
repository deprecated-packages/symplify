<?php declare(strict_types = 1);

namespace Symplify\SymfonyEventDispatcher\Adapter\Symfony\DependencyInjection\Extension;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class ContainerExtension extends Extension
{
    /**
     * @param mixed[] $configs
     * @param ContainerBuilder $containerBuilder
     */
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        (new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../../../../config')))
            ->load('services.neon');
    }
}
