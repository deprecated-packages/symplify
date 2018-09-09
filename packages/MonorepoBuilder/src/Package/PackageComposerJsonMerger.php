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

                $merged = $this->mergeSection($packageComposerJson, $section, $merged);
            }
        }

        return $merged;
    }

    /**
     * @param mixed[] $packageComposerJson
     * @param mixed[] $merged
     * @return mixed[]
     */
    private function mergeSection(array $packageComposerJson, string $section, array $merged): array
    {
        // array sections
        if (is_array($packageComposerJson[$section])) {
            $merged[$section] = $this->parametersMerger->merge(
                $merged[$section] ?? [],
                $packageComposerJson[$section]
            );

            return $merged;
        }

        // key: value sections, like "minimum-stability: dev"
        $merged[$section] = $packageComposerJson[$section];

        return $merged;
    }
}
