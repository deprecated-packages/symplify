<?php

declare(strict_types=1);

use Nette\Utils\Strings;
use Symplify\EasyCodingStandard\Application\Version\StaticVersionResolver;

require __DIR__ . '/vendor/autoload.php';

$timestamp = (new DateTime('now'))->format('Ymd');

// see https://github.com/humbug/php-scoper
return [
    'prefix' => 'ECSPrefix' . $timestamp,

    // excluded
    'exclude-namespaces' => [
        '#^Symplify\\\\EasyCodingStandard#',
        '#^Symplify\\\\CodingStandard#',
        '#^PhpCsFixer#',
        '#^PHP_CodeSniffer#',
    ],
    'expose-constants' => ['__ECS_RUNNING__'],

    'exclude-files' => [
        // do not prefix "trigger_deprecation" from symfony - https://github.com/symfony/symfony/commit/0032b2a2893d3be592d4312b7b098fb9d71aca03
        // these paths are relative to this file location, so it should be in the root directory
        'vendor/symfony/deprecation-contracts/function.php',
        'vendor/symfony/polyfill-intl-normalizer/bootstrap.php',
        'vendor/symfony/polyfill-intl-normalizer/bootstrap80.php',
        'vendor/symfony/polyfill-mbstring/bootstrap.php',
        'vendor/symfony/polyfill-mbstring/bootstrap80.php',
        'vendor/symfony/polyfill-php80/bootstrap.php',
        'vendor/symfony/polyfill-php80/Resources/stubs/Attribute.php',
        'vendor/symfony/polyfill-php80/Resources/stubs/PhpToken.php',
        'vendor/symfony/polyfill-php80/Resources/stubs/Stringable.php',
        'vendor/symfony/polyfill-php80/Resources/stubs/ValueError.php',
        'vendor/symfony/polyfill-php80/Resources/stubs/UnhandledMatchError.php',
    ],

    'expose-functions' => [
        'fdiv', 'preg_last_error_msg', 'str_contains', 'str_starts_with', 'str_ends_with', 'get_debug_type', 'get_resource_id',
    ],

    // expose
    'expose-classes' => [
        // part of public interface of configs.php
        'Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator',
        'Symplify\SmartFileSystem\SmartFileInfo',
    ],

    'patchers' => [
        // scope symfony configs
        function (string $filePath, string $prefix, string $content): string {
            if (! Strings::match($filePath, '#(packages|config|services)\.php$#')) {
                return $content;
            }

            // fix symfony config load scoping, except CodingStandard and EasyCodingStandard
            $content = Strings::replace(
                $content,
                '#load\(\'Symplify\\\\\\\\(?<package_name>[A-Za-z]+)#',
                function (array $match) use ($prefix) {
                    if (in_array($match['package_name'], ['CodingStandard', 'EasyCodingStandard'], true)) {
                        // skip
                        return $match[0];
                    }

                    return 'load(\'' . $prefix . '\Symplify\\' . $match['package_name'];
                }
            );

            return $content;
        },

        // fixes https://github.com/symplify/symplify/issues/3205
        function (string $filePath, string $prefix, string $content): string {
            if (! str_ends_with($filePath, 'src/Testing/PHPUnit/AbstractCheckerTestCase.php')) {
                return $content;
            }

            return Strings::replace(
                $content,
                '#' . $prefix . '\\\\PHPUnit\\\\Framework\\\\TestCase#',
                'PHPUnit\Framework\TestCase'
            );
        },

        // add static versions constant values
        function (string $filePath, string $prefix, string $content): string {
            if (! str_ends_with($filePath, 'src/Application/Version/StaticVersionResolver.php')) {
                return $content;
            }

            $releaseDateTime = StaticVersionResolver::resolverReleaseDateTime();

            return strtr($content, [
                '@package_version@' => StaticVersionResolver::resolvePackageVersion(),
                '@release_date@' => $releaseDateTime->format('Y-m-d H:i:s'),
            ]);
        },
    ],
];
