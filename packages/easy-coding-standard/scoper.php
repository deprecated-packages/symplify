<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Nette\Utils\DateTime;
use Nette\Utils\Strings;

$dateTime = DateTime::from('now');
$timestamp = $dateTime->format('Ymd');

// see https://github.com/humbug/php-scoper
return [
    'prefix' => 'ECSPrefix' . $timestamp,
    'files-whitelist' => [
        // do not prefix "trigger_deprecation" from symfony - https://github.com/symfony/symfony/commit/0032b2a2893d3be592d4312b7b098fb9d71aca03
        // these paths are relative to this file location, so it should be in the root directory
        'vendor/symfony/deprecation-contracts/function.php',
        // for package versions - https://github.com/symplify/easy-coding-standard-prefixed/runs/2176047833
    ],

    'whitelist' => [
        // needed for autoload, that is not prefixed, since it's in bin/* file
        'Symplify\*',
        'PhpCsFixer\*',
        'PHP_CodeSniffer\*',
        'Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator',
        'Symfony\Component\DependencyInjection\Extension\ExtensionInterface',
        'Composer\InstalledVersions',
    ],
    'patchers' => [
        // unprefix polyfill functions
        // @see https://github.com/humbug/php-scoper/issues/440#issuecomment-795160132
        function (string $filePath, string $prefix, string $content): string {
            if (Strings::match($filePath, '#vendor/symfony/polyfill-(.*)/bootstrap(.*?).php')) {
                return Strings::replace($content, 'namespace '. $prefix . ';', '');
            }

            return $content;
        },

        function (string $filePath, string $prefix, string $content): string {
            if (! Strings::endsWith($filePath, 'vendor/jean85/pretty-package-versions/src/PrettyVersions.php')) {
                return $content;
            }

            // see https://regex101.com/r/v8zRMm/1
            return Strings::replace(
                $content,
                '#' . $prefix . '\\\\Composer\\\\InstalledVersions#',
                'Composer\InstalledVersions'
            );
        },
        // fixes https://github.com/symplify/symplify/issues/3102
        function (string $filePath, string $prefix, string $content): string {
            if (! Strings::contains($filePath, 'vendor/')) {
                return $content;
            }

            // @see https://regex101.com/r/lBV8IO/2
            $fqcnReservedPattern = sprintf('#(\\\\)?%s\\\\(parent|self|static)#m', $prefix);
            $matches = Strings::matchAll($content, $fqcnReservedPattern);

            if (! $matches) {
                return $content;
            }

            foreach ($matches as $match) {
                $content = str_replace($match[0], $match[2], $content);
            }

            return $content;
        },
    ],
];
