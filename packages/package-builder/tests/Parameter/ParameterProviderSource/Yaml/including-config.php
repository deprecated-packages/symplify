<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/included-config.php');

    $parameters = $containerConfigurator->parameters();
    $parameters->set('two', 2);
};
