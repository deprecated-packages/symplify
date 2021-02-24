<?php

declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder;

$symfonyPolyfillAllowlist = \array_map(
    static function ($file) {
        return $file->getPathName();
    },
    \array_values(
        \iterator_to_array(
            Finder::create()
                ->files()
                ->in(__DIR__ . '/vendor/symfony/polyfill-*')
                ->name('*.php')
        )
    )
);

return [
    'files-whitelist' => [
        // do not prefix "trigger_deprecatoin" from symfony - https://github.com/symfony/symfony/commit/0032b2a2893d3be592d4312b7b098fb9d71aca03
        // these paths are relative to this file location, so it should be in the root directory
        'vendor/symfony/deprecation-contracts/function.php',
    ] + $symfonyPolyfillAllowlist,
    'whitelist' => [
        // needed for autoload, that is not prefixed, since it's in bin/* file
        'Symplify\*',
        'PhpCsFixer\*',
        'PHP_CodeSniffer\*',
        'SlevomatCodingStandard\*',
        'Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator',
        'Symfony\Component\DependencyInjection\Extension\ExtensionInterface',
        'Symfony\Polyfill\*',
    ],
];
