<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Configuration;

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
        return $this->packages;
    }
}
