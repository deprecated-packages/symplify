<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Testing\ComposerJson;

use Symplify\MonorepoBuilder\ValueObject\Section;

final class ComposerVersionManipulator
{
    /**
     * @param mixed[] $packageComposerJson
     * @param string[] $usedPackageNames
     * @return mixed[]
     */
    public function setAsteriskVersionForUsedPackages(array $packageComposerJson, array $usedPackageNames): array
    {
        foreach ([Section::REQUIRE, Section::REQUIRE_DEV] as $section) {
            foreach ($usedPackageNames as $usedPackageName) {
                if (! isset($packageComposerJson[$section][$usedPackageName])) {
                    continue;
                }

                $packageComposerJson[$section][$usedPackageName] = '*';
            }
        }

        return $packageComposerJson;
    }
}
