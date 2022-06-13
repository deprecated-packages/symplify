<?php

declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder;

require __DIR__ . '/vendor/autoload.php';

$nowDateTime = new DateTime('now');
$timestamp = $nowDateTime->format('Ym');

// @see https://github.com/humbug/php-scoper/blob/master/docs/further-reading.md
use Nette\Utils\Strings;

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
    'prefix' => 'VendorPatches' . $timestamp,
    'expose-constants' => ['#^SYMFONY\_[\p{L}_]+$#'],
    'exclude-namespaces' => ['#^Symplify\\\\VendorPatches#'],
    'exclude-files' => [
        // do not prefix "trigger_deprecation" from symfony - https://github.com/symfony/symfony/commit/0032b2a2893d3be592d4312b7b098fb9d71aca03
        // these paths are relative to this file location, so it should be in the root directory
        'vendor/symfony/deprecation-contracts/function.php',
        ...$polyfillsBootstraps,
        ...$polyfillsStubs,
    ],
    'patchers' => [
        // scope symfony configs
        function (string $filePath, string $prefix, string $content): string {
            if (! Strings::match($filePath, '#(packages|config|services)\.php$#')) {
                return $content;
            }

            // fix symfony config load scoping, except EasyCI
            $content = Strings::replace(
                $content,
                '#load\(\'Symplify\\\\\\\\(?<package_name>[A-Za-z]+)#',
                function (array $match) use ($prefix) {
                    if (in_array($match['package_name'], ['VendorPatches'], true)) {
                        // skip
                        return $match[0];
                    }

                    return 'load(\'' . $prefix . '\Symplify\\' . $match['package_name'];
                }
            );

            return $content;
        },
    ],
];
