<?php

declare(strict_types=1);

use Nette\Utils\Strings;

require __DIR__ . '/vendor/autoload.php';

$timestamp = (new DateTime('now'))->format('Ymd');

// see https://github.com/humbug/php-scoper
return [
    'prefix' => 'MonorepoBuilder' . $timestamp,
    'exclude-files' => [
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
    'exclude-namespaces' => [
        // needed for autoload, that is not prefixed, since it's in bin/* file
        '#^Symplify\MonorepoBuilder\*#',
        // part of public API in \Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface
        '#^PharIo\Version\*#',
        // needed by the monorepo-builder command (avoid failing with a "class not found" error)
    ],
    'expose-classes' => [
        // part of public interface of configs.php
        'Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator',
        'Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection',
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
                    return 'load(\'' . $prefix . '\Symplify\\' . $match['package_name'];
                }
            );

            return $content;
        },

        // scope symfony configs
        function (string $filePath, string $prefix, string $content): string {
            if (! Strings::match($filePath, '#(packages|config|services)\.php$#')) {
                return $content;
            }

            // unprefix symfony config
            return Strings::replace(
                $content,
                '#load\(\'' . $prefix . '\\\\Symplify\\\\MonorepoBuilder#',
                'load(\'' . 'Symplify\\MonorepoBuilder',
            );
        },
    ],
];
