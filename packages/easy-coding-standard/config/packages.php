<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Caching\Cache;
use Symplify\EasyCodingStandard\Caching\CacheFactory;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\EasyCodingStandard\\', __DIR__ . '/../packages')
        ->exclude([
            '*/Exception/*',
            '*/ValueObject/*',
            __DIR__ . '/../packages/SniffRunner/ValueObject/File.php',
            __DIR__ . '/../packages/Caching/ValueObject/',
            __DIR__ . '/../packages/Caching/Cache.php',
        ]);

    $services->set(Cache::class)
        ->factory([service(CacheFactory::class), 'create']);
};
