<?php

declare(strict_types=1);

use Nette\Utils\Strings;
use Symplify\EasyCodingStandard\Application\Version\StaticVersionResolver;

require __DIR__ . '/vendor/autoload.php';

$timestamp = (new DateTime('now'))->format('Ymd');

use Isolated\Symfony\Component\Finder\Finder;

// excluding polyfills in generic way
// @see https://github.com/humbug/php-scoper/blob/cb23986d9309a10eaa284242f2169723af4e4a7e/docs/further-reading.md#further-reading

$polyfillsBootstraps = array_map(
    static fn (SplFileInfo $fileInfo) => $fileInfo->getPathname(),
    iterator_to_array(
        Finder::create()
            ->files()
            ->in(__DIR__ . '/vendor/symfony/polyfill-*')
            ->name('bootstrap*.php'),
        false,
    ),
);

$polyfillsStubs = array_map(
    static fn (SplFileInfo $fileInfo) => $fileInfo->getPathname(),
    iterator_to_array(
        Finder::create()
            ->files()
            ->in(__DIR__ . '/vendor/symfony/polyfill-*/Resources/stubs')
            ->name('*.php'),
        false,
    ),
);

// see https://github.com/humbug/php-scoper
return [
    'prefix' => 'ECSPrefix' . $timestamp,

    // excluded
    'exclude-namespaces' => [
        '#^Symplify\\\\EasyCodingStandard#',
        '#^Symplify\\\\CodingStandard#',
        '#^PhpCsFixer#',
        '#^PHP_CodeSniffer#',
        '#^Symfony\\\\Polyfill#'
    ],
    'exclude-constants' => [
        // Symfony global constants
        '#^SYMFONY\_[\p{L}_]+$#',
    ],
    'expose-constants' => ['__ECS_RUNNING__'],

    'exclude-files' => [
        ...$polyfillsBootstraps,
        ...$polyfillsStubs,
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
