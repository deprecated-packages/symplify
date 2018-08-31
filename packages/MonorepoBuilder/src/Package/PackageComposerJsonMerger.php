<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Package;

use Symfony\Component\Finder\SplFileInfo;
use Symplify\MonorepoBuilder\Configuration\MergedPackagesCollector;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;
use Symplify\PackageBuilder\Yaml\ParametersMerger;

final class PackageComposerJsonMerger
{
    /**
     * @var ParametersMerger
     */
    private $parametersMerger;

    /**
     * @var MergedPackagesCollector
     */
    private $mergedPackagesCollector;

    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    public function __construct(
        ParametersMerger $parametersMerger,
        MergedPackagesCollector $mergedPackagesCollector,
        JsonFileManager $jsonFileManager
    ) {
        $this->parametersMerger = $parametersMerger;
        $this->mergedPackagesCollector = $mergedPackagesCollector;
        $this->jsonFileManager = $jsonFileManager;
    }

    /**
     * @param SplFileInfo[] $composerPackageFileInfos
     * @param string[] $sections
     * @return string[]
     */
    public function mergeFileInfos(array $composerPackageFileInfos, array $sections): array
    {
        $merged = [];

        foreach ($composerPackageFileInfos as $packageFile) {
            $packageComposerJson = $this->jsonFileManager->loadFromFileInfo($packageFile);

            if (isset($packageComposerJson['name'])) {
                $this->mergedPackagesCollector->addPackage($packageComposerJson['name']);
            }

            foreach ($sections as $section) {
                if (! isset($packageComposerJson[$section])) {
                    continue;
                }

                $merged[$section] = $this->parametersMerger->merge(
                    $merged[$section] ?? [],
                    $packageComposerJson[$section]
                );
            }
        }

        return $merged;
    }
}
