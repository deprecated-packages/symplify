<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Adapter\Symfony\DependencyInjection\Extension;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symplify\ModularDoctrineFilters\Adapter\Symfony\DependencyInjection\DefinitionFinder;
use Symplify\ModularDoctrineFilters\EventSubscriber\EnableFiltersSubscriber;

final class ModularDoctrineFiltersExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $containerBuilder) : void
    {
        (new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../../../../config')))
            ->load('services.neon');

        DefinitionFinder::getByType($containerBuilder, EnableFiltersSubscriber::class)
            ->addTag('kernel.event_subscriber');
    }
}
