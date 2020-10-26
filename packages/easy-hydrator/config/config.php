<?php

declare(strict_types=1);

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Contracts\Cache\CacheInterface;
use Symplify\EasyHydrator\ParameterValueGetter\DefaultParameterValueGetter;
use Symplify\EasyHydrator\ParameterValueGetter\ParameterValueGetterInterface;
use Symplify\PackageBuilder\Strings\StringFormatConverter;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\\EasyHydrator\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/EasyHydratorBundle.php']);

    $services->set(FilesystemAdapter::class);
    $services->alias(CacheInterface::class, FilesystemAdapter::class);

    $services->set(StringFormatConverter::class);

    $services->alias(ParameterValueGetterInterface::class, DefaultParameterValueGetter::class);
};
