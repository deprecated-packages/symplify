<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Package;

use Symfony\Component\Finder\SplFileInfo;
use Symplify\MonorepoBuilder\Composer\Section;
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

        return $this->filterOutDuplicatesRequireAndRequireDev($merged);
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

            // uniquate special cases, ref https://github.com/Symplify/Symplify/issues/1197
            if ($section === 'repositories') {
                $merged[$section] = array_unique($merged[$section], SORT_REGULAR);
            }

            return $merged;
        }

        // key: value sections, like "minimum-stability: dev"
        $merged[$section] = $packageComposerJson[$section];

        return $merged;
    }

    /**
     * @param mixed[] $composerJson
     * @return mixed[]
     */
    private function filterOutDuplicatesRequireAndRequireDev(array $composerJson): array
    {
        if (! isset($composerJson[Section::REQUIRE]) || ! isset($composerJson[Section::REQUIRE_DEV])) {
            return $composerJson;
        }

        $duplicatedPackages = array_intersect(
            array_keys($composerJson[Section::REQUIRE]),
            array_keys($composerJson[Section::REQUIRE_DEV])
        );

        foreach (array_keys($composerJson[Section::REQUIRE_DEV]) as $package) {
            if (in_array($package, $duplicatedPackages, true)) {
                unset($composerJson[Section::REQUIRE_DEV][$package]);
            }
        }

        // remove empty "require-dev"
        if (! count($composerJson[Section::REQUIRE_DEV])) {
            unset($composerJson[Section::REQUIRE_DEV]);
        }

        return $composerJson;
    }
}
