<?php

// @see https://github.com/composer-unused/composer-unused

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Configuration\NamedFilter;

return static function (Configuration $config): Configuration {
    // required to install patches
    $config->addNamedFilter(NamedFilter::fromString('cweagans/composer-patches'));

    // required by ECS
    $config->addNamedFilter(NamedFilter::fromString('squizlabs/php_codesniffer'));

    return $config;
};
