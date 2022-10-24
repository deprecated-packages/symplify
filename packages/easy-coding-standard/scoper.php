<?php

declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder;
use Nette\Utils\Strings;

require __DIR__ . '/vendor/autoload.php';

$timestamp = (new DateTime('now'))->format('Ym');

use Symplify\EasyCodingStandard\Application\Version\StaticVersionResolver;

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
        '#^Symfony\\\\Polyfill#',
    ],
    'exclude-constants' => [
        // Symfony global constants
        '#^SYMFONY\_[\p{L}_]+$#',
        // TOKENS from code sniffer - https://github.com/symplify/easy-coding-standard/blob/main/vendor/squizlabs/php_codesniffer/src/Util/Tokens.php
        '#^T_(.*?)#',
        'PHP_CODESNIFFER_CBF',
        'PHP_CODESNIFFER_VERBOSITY',
    ],
    'expose-constants' => ['__ECS_RUNNING__'],
    'expose-functions' => ['u', 'b', 's', 'trigger_deprecation'],

    'exclude-files' => [...$polyfillsBootstraps, ...$polyfillsStubs],

    // expose
    'expose-classes' => [
        'Normalizer',
        // part of public interface of configs.php
        'Symplify\SmartFileSystem\SmartFileInfo',
    ],

    'patchers' => [
        // fix symfony deprecation reports, @see https://github.com/symplify/symplify/issues/4449
        static function (string $filePath, string $prefix, string $content): string {
            if (! \str_ends_with($filePath, 'vendor/symfony/deprecation-contracts/function.php')) {
                return $content;
            }

            // comment out
            return str_replace('@\trigger_', '// @\trigger_', $content);
        },

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
