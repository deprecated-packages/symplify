<?php

declare(strict_types=1);

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\Console\MonorepoBuilderConsoleApplication;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;
use Symplify\PackageBuilder\Yaml\ParametersMerger;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\MonorepoBuilder\\', __DIR__ . '/../src')
        ->exclude([
            __DIR__ . '/../src/Exception',
            __DIR__ . '/../src/HttpKernel',
            __DIR__ . '/../src/ValueObject',
        ]);

    // console
    $services->set(MonorepoBuilderConsoleApplication::class);
    $services->alias(Application::class, MonorepoBuilderConsoleApplication::class);
    $services->set(CommandNaming::class);

    $services->set(PrivatesCaller::class);
    $services->set(ParametersMerger::class);
};
