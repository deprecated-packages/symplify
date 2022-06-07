<?php

declare(strict_types=1);

use Nette\Utils\Strings;

require __DIR__ . '/vendor/autoload.php';

$timestamp = (new DateTime('now'))->format('Ymd');

// see https://github.com/humbug/php-scoper
return [
    'prefix' => 'ConfigTransformer' . $timestamp . random_int(0, 10),
    'expose-classes' => [
        // part of public interface of configs.php
        'Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator',
    ],

    // excluded
    'excluded-namespaces' => ['#^Symplify\ConfigTransformer#'],
    'excluded-files' => [
        // these paths are relative to this file location, so it should be in the root directory
        'vendor/symfony/deprecation-contracts/function.php',
        'vendor/symfony/polyfill-php80/Resources/stubs/Attribute.php',
        'vendor/symfony/polyfill-php80/Resources/stubs/PhpToken.php',
        'vendor/symfony/polyfill-php80/Resources/stubs/Stringable.php',
        'vendor/symfony/polyfill-php80/Resources/stubs/ValueError.php',
        'vendor/symfony/polyfill-php80/Resources/stubs/UnhandledMatchError.php',
    ],
    'patchers' => [
        // unprefix strings used for config printing
        // fixes https://github.com/symplify/symplify/issues/3976
        function (string $filePath, string $prefix, string $content): string {
            if (! Strings::match($filePath, '#Symplify\/PhpConfigPrinter\/ValueObject\/FunctionName\.php#')) {
                return $content;
            }

            $pattern = sprintf('#public const (.*?) = \'%s\\\\\\\\#', $prefix);

            return Strings::replace($content, $pattern, 'public const $1 = \'');
        },
    ],
];
