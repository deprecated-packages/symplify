<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/../config/packages/*.php');

    $parameters = $containerConfigurator->parameters();
    $parameters->set('kernel.secret', '123');

    $services = $containerConfigurator->services();
    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\SymfonyStaticDumper\Tests\TestProject\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/HttpKernel']);
};
