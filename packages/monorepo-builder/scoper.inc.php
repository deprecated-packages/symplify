<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return [
    'prefix' => null,
    'finders' => [],
    'files-whitelist' => [
        // do not prefix "trigger_deprecatoin" from symfony - https://github.com/symfony/symfony/commit/0032b2a2893d3be592d4312b7b098fb9d71aca03
        // these paths are relative to this file location, so it should be in the root directory
        'vendor/symfony/deprecation-contracts/function.php',
        // avoid pre-slashing everything
        'composer.json',
    ],
    'whitelist' => [
        // needed for autoload, that is not prefixed, since it's in bin/* file
        'Symplify\*',
        // for config.php
        ContainerConfigurator::class,
        'Composer\*',
    ],
];
