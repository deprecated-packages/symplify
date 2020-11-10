<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Rules\CheckRequiredAutowireAutoconfigurePublicUsedInConfigServiceRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(CheckRequiredAutowireAutoconfigurePublicUsedInConfigServiceRule::class);
};

?>
-----
services:
    -
        class: Symplify\PHPStanRules\Rules\CheckRequiredAutowireAutoconfigurePublicUsedInConfigServiceRule
        tags: [phpstan.rules.rule]
