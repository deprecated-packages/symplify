<?php

declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder;

require __DIR__ . '/vendor/autoload.php';

$timestamp = (new DateTime('now'))->format('Ymd');

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
    'prefix' => 'ConfigTransformer' . $timestamp,
    'expose-classes' => [
        // part of public interface of configs.php
        'Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator',
    ],
    'expose-constants' => ['#^SYMFONY\_[\p{L}_]+$#'],

    // excluded
    'exclude-namespaces' => [
        '#^Symplify\\\\ConfigTransformer#',
        '#^Symplify\\\\PhpConfigPrinter#',
        '#^Symfony\\\\Polyfill#',
    ],
    'exclude-files' => [
        // these paths are relative to this file location, so it should be in the root directory
        'vendor/symfony/deprecation-contracts/function.php',
        ...$polyfillsBootstraps,
        ...$polyfillsStubs,
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
