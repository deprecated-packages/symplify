<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\NoChainMethodCallRule\Fixture;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();
};
