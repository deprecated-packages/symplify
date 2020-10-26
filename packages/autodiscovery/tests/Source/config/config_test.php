<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/packages/*.yaml');

    $parameters = $containerConfigurator->parameters();
    $parameters->set('kernel.secret', 123);
};
