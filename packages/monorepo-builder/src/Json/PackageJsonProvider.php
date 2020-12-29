<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Json;

use Nette\Utils\Strings;
use Symplify\MonorepoBuilder\Package\PackageProvider;
use Symplify\MonorepoBuilder\Parameter\ParameterSupplier;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class PackageJsonProvider
{
    /**
     * @var PackageProvider
     */
    private $packageProvider;

    /**
     * @var string[]
     */
    private $packageDirectoriesData = [];

    public function __construct(
        PackageProvider $packageProvider,
        ParameterProvider $parameterProvider,
        ParameterSupplier $parameterSupplier
    ) {
        $this->packageProvider = $packageProvider;
        $this->packageDirectoriesData = $parameterSupplier->fillPackageDirectoriesWithDefaultData(
            $parameterProvider->provideArrayParameter(Option::PACKAGE_DIRECTORIES)
        );
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
                'organization' => $this->getRepoOwnerForPackageDirectory($package->getRealPath()),
            ];
        }

        return $packageEntries;
    }

    /**
     * @return array<string[]>
     */
    private function getRepoOwnerForPackageDirectory(string $packageRealPath): string
    {
        // Iterate all entries until finding the one for the package's path
        foreach ($this->packageDirectoriesData as $packageDirectory => $packageData) {
            if (Strings::startsWith($packageRealPath, $packageDirectory)) {
                return (string) $packageData['organization'];
            }
        }

        throw new ShouldNotHappenException(
            sprintf('There is no organization for the package under path "%s"', $packageRealPath)
        );
    }
}
