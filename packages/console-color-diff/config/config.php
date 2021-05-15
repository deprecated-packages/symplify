<?php

declare(strict_types=1);

use SebastianBergmann\Diff\Differ;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\ConsoleColorDiff\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/Bundle']);

    $services->set(Differ::class);
    $services->set(PrivatesAccessor::class);
};
