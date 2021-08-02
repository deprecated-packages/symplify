<?php

declare(strict_types=1);

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PHPUnitUpgrader\Console\PHPUnitUpgraderConsoleApplication;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\PHPUnitUpgrader\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/HttpKernel', __DIR__ . '/../src/ValueObject']);

    $services->alias(Application::class, PHPUnitUpgraderConsoleApplication::class);
    $services->set(CommandNaming::class);
};
