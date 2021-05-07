<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Configuration;

final class MergedPackagesCollector
{
    /**
     * @var string[]
     */
    private $packages = [];

    public function addPackage(string $package): void
    {
        $this->packages[] = $package;
    }

    /**
     * @return string[]
     */
    public function getPackages(): array
    {
        $this->packages = array_unique($this->packages);

        sort($this->packages);

        return $this->packages;
    }
}
