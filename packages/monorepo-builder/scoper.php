<?php

declare(strict_types=1);

use Nette\Utils\Strings;

require __DIR__ . '/vendor/autoload.php';

/**
 * @see https://regex101.com/r/LMDq0p/1
 * @var string
 */
const POLYFILL_FILE_NAME_REGEX = '#vendor\/symfony\/polyfill\-(.*)\/bootstrap(.*?)\.php#';

/**
 * @see https://regex101.com/r/RBZ0bN/1
 * @var string
 */
const POLYFILL_STUBS_NAME_REGEX = '#vendor\/symfony\/polyfill\-(.*)\/Resources\/stubs#';

$timestamp = (new DateTime('now'))->format('Ymd');

// see https://github.com/humbug/php-scoper
return [
    'prefix' => 'MonorepoBuilder' . $timestamp,
    'files-whitelist' => [
        // do not prefix "trigger_deprecation" from symfony - https://github.com/symfony/symfony/commit/0032b2a2893d3be592d4312b7b098fb9d71aca03
        // these paths are relative to this file location, so it should be in the root directory
        'vendor/symfony/deprecation-contracts/function.php',
        // for package versions - https://github.com/symplify/easy-coding-standard-prefixed/runs/2176047833
    ],
    'patchers' => [
        // unprefix polyfill functions
        // @see https://github.com/humbug/php-scoper/issues/440#issuecomment-795160132
        function (string $filePath, string $prefix, string $content): string {
            if (! Strings::match($filePath, POLYFILL_FILE_NAME_REGEX)) {
                return $content;
            }

            $content = Strings::replace($content, '#namespace ' . $prefix . ';#', '');

            // add missing use statements prefixes
            // @see https://github.com/symplify/easy-coding-standard/commit/5c11eca46fbe341ac30d0d5da2c51e1596950299#diff-87ecc51ebcf33f4c2699c08f35403560ad1ea98d22771df83a29d00dc5f53a1cR12
            return Strings::replace($content, '#use Symfony\\\\Polyfill#', 'use ' . $prefix . ' Symfony\Polyfill');
        },
        // remove namespace frompoly fill stubs
        function (string $filePath, string $prefix, string $content): string {
            if (! Strings::match($filePath, POLYFILL_STUBS_NAME_REGEX)) {
                return $content;
            }

            // remove alias to class have origina PHP names - fix in
            $content = Strings::replace($content, '#\\\\class_alias(.*?);#', '');

            return Strings::replace($content, '#namespace ' . $prefix . ';#', '');
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
