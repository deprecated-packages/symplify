<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\MonorepoBuilder\\', __DIR__ . '/../packages')
        ->exclude([
            // register manually
            __DIR__ . '/../packages/Release/ReleaseWorker',
        ]);
};
