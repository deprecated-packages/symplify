<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanPHPConfig\Tests\PHPStanPHPToNeonConverter\Source\WithConfigurationRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(WithConfigurationRule::class)
        ->arg('$someValue', 10);
};

?>
-----
services:
    -
        class: Symplify\PHPStanPHPConfig\Tests\PHPStanPHPToNeonConverter\Source\WithConfigurationRule
        tags: [phpstan.rules.rule]
        arguments:
            $someValue: 10
