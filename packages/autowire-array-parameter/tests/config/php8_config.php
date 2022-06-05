<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->public();

    $services->load('Symplify\AutowireArrayParameter\Tests\SourcePhp8\\', __DIR__ . '/../SourcePhp8')
        ->exclude([__DIR__ . '/../SourcePhp8/SkipMe']);
};
