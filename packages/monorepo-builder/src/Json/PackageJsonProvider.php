<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Json;

use Symplify\MonorepoBuilder\Package\PackageProvider;

final class PackageJsonProvider
{
    /**
     * @var PackageProvider
     */
    private $packageProvider;

    public function __construct(PackageProvider $packageProvider)
    {
        $this->packageProvider = $packageProvider;
    }

    /**
     * @return array<string[]>
     */
    public function providePackageEntries(): array
    {
        $packageEntries = [];
        foreach ($this->packageProvider->provide() as $package) {
            $packageEntries[] = [
                'name' => $package->getShortName(),
                'path' => $package->getRelativePath(),
            ];
        }

        return $packageEntries;
    }
}
