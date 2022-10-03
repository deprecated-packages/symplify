<?php

// @see https://github.com/composer-unused/composer-unused

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Configuration\NamedFilter;

return static function (Configuration $config): Configuration {
    // required to install patches
    $config->addNamedFilter(NamedFilter::fromString('cweagans/composer-patches'));

    // needed for config transformer and legacy symfony configs
    $config->addNamedFilter(NamedFilter::fromString('symfony/expression-language'));

    // required by ECS
    $config->addNamedFilter(NamedFilter::fromString('squizlabs/php_codesniffer'));

    return $config;
};
