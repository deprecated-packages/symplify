<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Symfony\Rector\Class_\ChangeFileLoaderInExtensionAndKernelRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ChangeFileLoaderInExtensionAndKernelRector::class)
        ->arg('$from', 'yaml')
        ->arg('$to', 'php');

    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::PATHS, [__DIR__ . '/packages/*/src/HttpKernel']);
};
