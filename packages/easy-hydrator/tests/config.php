<?php

declare(strict_types=1);

use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Contracts\Cache\CacheInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->set(NullAdapter::class);
    $services->alias(CacheInterface::class, NullAdapter::class);
};
