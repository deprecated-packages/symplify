<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\SnifferFixerToECSConverter\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/HttpKernel']);

    $services->set(ClassLikeExistenceChecker::class);
};
