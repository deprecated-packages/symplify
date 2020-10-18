<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Testing\PackageDependency;

use Symplify\MonorepoBuilder\Package\PackageNamesProvider;
use Symplify\MonorepoBuilder\ValueObject\Section;

final class UsedPackagesResolver
{
    /**
     * @var PackageNamesProvider
     */
    private $packageNamesProvider;

    public function __construct(PackageNamesProvider $packageNamesProvider)
    {
        $this->packageNamesProvider = $packageNamesProvider;
    }

    /**
     * @param mixed[] $packageComposerJson
     * @return string[]
     */
    public function resolveForPackage(array $packageComposerJson): array
    {
        $usedPackageNames = [];

        foreach ([Section::REQUIRE, Section::REQUIRE_DEV] as $section) {
            if (! isset($packageComposerJson[$section])) {
                continue;
            }

            $sectionKeys = array_keys($packageComposerJson[$section]);
            foreach ($sectionKeys as $packageName) {
                if (! in_array($packageName, $this->packageNamesProvider->provide(), true)) {
                    continue;
                }

                $usedPackageNames[] = $packageName;
            }
        }

        return $usedPackageNames;
    }
}
