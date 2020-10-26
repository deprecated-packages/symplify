<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Strict\StrictParamFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(StrictParamFixer::class)
        ->call('configure', [[
            'be_strict' => 'yea',
        ]]);
};
