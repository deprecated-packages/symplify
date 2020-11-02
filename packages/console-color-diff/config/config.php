<?php

declare(strict_types=1);

use SebastianBergmann\Diff\Differ;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use function Symplify\PackageBuilder\Functions\service_polyfill;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\ConsoleColorDiff\\', __DIR__ . '/../src');

    $services->set(Differ::class);

    $services->set(SymfonyStyleFactory::class);

    $services->set(SymfonyStyle::class)
        ->factory([service_polyfill(SymfonyStyleFactory::class), 'create']);
};
