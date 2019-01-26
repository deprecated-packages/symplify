<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Package;

use Symplify\MonorepoBuilder\ArraySorter;
use Symplify\MonorepoBuilder\Composer\Section;
use Symplify\MonorepoBuilder\Configuration\MergedPackagesCollector;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\PackageBuilder\Yaml\ParametersMerger;
use function Safe\getcwd;

final class PackageComposerJsonMerger
{
    /**
     * @var string[]
     */
    private $mergeSections = [];

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

    /**
     * @var ArraySorter
     */
    private $arraySorter;

    /**
     * @param string[] $mergeSections
     */
    public function __construct(
        ParametersMerger $parametersMerger,
        MergedPackagesCollector $mergedPackagesCollector,
        JsonFileManager $jsonFileManager,
        ArraySorter $arraySorter,
        array $mergeSections
    ) {
        $this->parametersMerger = $parametersMerger;
        $this->mergedPackagesCollector = $mergedPackagesCollector;
        $this->jsonFileManager = $jsonFileManager;
        $this->mergeSections = $mergeSections;
        $this->arraySorter = $arraySorter;
    }

    /**
     * @param SmartFileInfo[] $composerPackageFileInfos
     * @return string[]
     */
    public function mergeFileInfos(array $composerPackageFileInfos): array
    {
        $merged = [];

        foreach ($composerPackageFileInfos as $packageFile) {
            $packageComposerJson = $this->jsonFileManager->loadFromFileInfo($packageFile);

            if (isset($packageComposerJson['name'])) {
                $this->mergedPackagesCollector->addPackage($packageComposerJson['name']);
            }

            foreach ($this->mergeSections as $mergeSection) {
                if (! isset($packageComposerJson[$mergeSection])) {
                    continue;
                }

                $packageComposerJson = $this->prepareAutoloadClassmapAndFiles(
                    $mergeSection,
                    $packageComposerJson,
                    $packageFile
                );

                $merged = $this->mergeSection($packageComposerJson, $mergeSection, $merged);
            }
        }

        return $this->filterOutDuplicatesRequireAndRequireDev($merged);
    }

    /**
     * Class map path needs to be prefixed before merge, otherwise will override one another
     * @see https://github.com/Symplify/Symplify/issues/1333
     * @param mixed[] $packageComposerJson
     * @return mixed[]
     */
    private function prepareAutoloadClassmapAndFiles(
        string $mergeSection,
        array $packageComposerJson,
        SmartFileInfo $packageFile
    ): array {
        if (! in_array($mergeSection, ['autoload', 'autoload-dev'], true)) {
            return $packageComposerJson;
        }

        if (isset($packageComposerJson[$mergeSection]['classmap'])) {
            $packageComposerJson[$mergeSection]['classmap'] = $this->relativizePath(
                $packageComposerJson[$mergeSection]['classmap'],
                $packageFile
            );
        }

        if (isset($packageComposerJson[$mergeSection]['exclude-from-classmap'])) {
            $packageComposerJson[$mergeSection]['exclude-from-classmap'] = $this->relativizePath(
                $packageComposerJson[$mergeSection]['exclude-from-classmap'],
                $packageFile
            );
        }

        if (isset($packageComposerJson[$mergeSection]['files'])) {
            $packageComposerJson[$mergeSection]['files'] = $this->relativizePath(
                $packageComposerJson[$mergeSection]['files'],
                $packageFile
            );
        }

        return $packageComposerJson;
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
            $merged[$section] = $this->parametersMerger->mergeWithCombine(
                $merged[$section] ?? [],
                $packageComposerJson[$section]
            );

            $merged[$section] = $this->arraySorter->recursiveSort($merged[$section]);

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

    /**
     * @param mixed[] $classmap
     * @return mixed[]
     */
    private function relativizePath(array $classmap, SmartFileInfo $packageFileInfo): array
    {
        $packageRelativeDirectory = dirname($packageFileInfo->getRelativeFilePathFromDirectory(getcwd()));
        foreach ($classmap as $key => $value) {
            $classmap[$key] = $packageRelativeDirectory . '/' . ltrim($value, '/');
        }

        return $classmap;
    }
}
