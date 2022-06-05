<?php

declare(strict_types=1);

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyTesting\Command\ValidateFixtureSkipNamingCommand;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire();

    $services->load('Symplify\EasyTesting\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/DataProvider', __DIR__ . '/../src/Kernel', __DIR__ . '/../src/ValueObject']);

    // console
    $services->set(Application::class)
        ->call('add', [service(ValidateFixtureSkipNamingCommand::class)]);
};
