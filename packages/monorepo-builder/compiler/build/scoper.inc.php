<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return [
    'prefix' => null,
    'finders' => [],
    'patchers' => [
        function (string $filePath, string $prefix, string $content): string {
            if ($filePath !== 'bin/monorepo-builder') {
                return $content;
            }
            return str_replace("__DIR__ . '/..", "'phar://monorepo-builder.phar", $content);
        },
    ],
    'whitelist' => [
        // needed for autoload, that is not prefixed, since it's in bin/* file
        'Symplify\*',
        // for config.php
        ContainerConfigurator::class,
        'Composer\*',
    ],
];
