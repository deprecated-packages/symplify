<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanPHPConfig\Tests\PHPStanPHPToNeonConverter\Source\SimpleRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(SimpleRule::class);
};

?>
-----
services:
    -
        class: Symplify\PHPStanPHPConfig\Tests\PHPStanPHPToNeonConverter\Source\SimpleRule
        tags: [phpstan.rules.rule]
