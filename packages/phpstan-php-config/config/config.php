<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PackageBuilder\Neon\NeonPrinter;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\\PHPStanPHPConfig\\', __DIR__ . '/../src')
        ->exclude([
            __DIR__ . '/../src/HttpKernel',
            __DIR__ . '/../src/ValueObject',
            __DIR__ . '/../src/Config/Loader',
        ]);

    $services->set(NeonPrinter::class);
};
