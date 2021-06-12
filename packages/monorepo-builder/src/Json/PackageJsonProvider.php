<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Json;

use Symplify\MonorepoBuilder\Package\PackageProvider;

final class PackageJsonProvider
{
    public function __construct(
        private PackageProvider $packageProvider
    ) {
    }

    /**
     * @return string[]
     */
    public function providePackages(): array
    {
        $packageShortNames = [];
        foreach ($this->packageProvider->provide() as $package) {
            $packageShortNames[] = $package->getShortName();
        }

        return $packageShortNames;
    }

    /**
     * @return string[]
     */
    public function providePackagesWithTests(): array
    {
        $packageShortNames = [];
        foreach ($this->packageProvider->provide() as $package) {
            if (! $package->hasTests()) {
                continue;
            }

            $packageShortNames[] = $package->getShortName();
        }

        return $packageShortNames;
    }
}
