<?php

declare(strict_types=1);

namespace Symplify\ComposerJsonManipulator\Sorter;

use Nette\Utils\Strings;

/**
 * Mostly inspired by https://github.com/composer/composer/blob/master/src/Composer/Json/JsonManipulator.php
 */
final class ComposerPackageSorter
{
    /**
     * @see https://regex101.com/r/tMrjMY/1
     * @var string
     */
    private const PLATFORM_PACKAGE_REGEX = '#^(?:php(?:-64bit|-ipv6|-zts|-debug)?|hhvm|(?:ext|lib)-[a-z0-9](?:[_.-]?[a-z0-9]+)*|composer-(?:plugin|runtime)-api)$#iD';

    /**
     * @see https://regex101.com/r/SXZcfb/1
     * @var string
     */
    private const REQUIREMENT_TYPE_REGEX = '#^(?<name>php|hhvm|ext|lib|\D)#';

    /**
     * Sorts packages by importance (platform packages first, then PHP dependencies) and alphabetically.
     * @link https://getcomposer.org/doc/02-libraries.md#platform-packages
     *
     * @param string[] $packages
     * @return string[]
     */
    public function sortPackages(array $packages = []): array
    {
        uksort($packages, function (string $firstPackageName, string $secondPackageName): int {
            return $this->createRequirement($firstPackageName) <=> $this->createRequirement($secondPackageName);
        });

        return $packages;
    }

    private function createRequirement(string $requirement): string
    {
        if ($this->isPlatformPackage($requirement)) {
            return (string) Strings::replace(
                $requirement,
                self::REQUIREMENT_TYPE_REGEX,
                function (array $match): string {
                    $name = $match['name'];
                    if ($name === 'php' || $name === 'hhvm') {
                        return '0-' . $name;
                    }
                    if ($name === 'ext') {
                        return '1-' . $name;
                    }
                    if ($name === 'lib') {
                        return '2-' . $name;
                    }

                    return '3-' . $name;
                }
            );
        }

        return '4-' . $requirement;
    }

    private function isPlatformPackage(string $name): bool
    {
        return (bool) Strings::match($name, self::PLATFORM_PACKAGE_REGEX);
    }
}
