<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge;

use Symplify\MonorepoBuilder\ComposerJsonObject\ComposerJsonFactory;
use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Arrays\ArraySorter;
use Symplify\MonorepoBuilder\Merge\Configuration\MergedPackagesCollector;
use Symplify\MonorepoBuilder\Merge\PathResolver\AutoloadPathNormalizer;
use Symplify\PackageBuilder\Yaml\ParametersMerger;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ComposerJsonMerger
{
    /**
     * @var MergedPackagesCollector
     */
    private $mergedPackagesCollector;

    /**
     * @var ParametersMerger
     */
    private $parametersMerger;

    /**
     * @var ArraySorter
     */
    private $arraySorter;

    /**
     * @var AutoloadPathNormalizer
     */
    private $autoloadPathNormalizer;

    /**
     * @var ComposerJsonFactory
     */
    private $composerJsonFactory;

    public function __construct(
        ComposerJsonFactory $composerJsonFactory,
        MergedPackagesCollector $mergedPackagesCollector,
        ParametersMerger $parametersMerger,
        ArraySorter $arraySorter,
        AutoloadPathNormalizer $autoloadPathNormalizer
    ) {
        $this->mergedPackagesCollector = $mergedPackagesCollector;
        $this->parametersMerger = $parametersMerger;
        $this->arraySorter = $arraySorter;
        $this->autoloadPathNormalizer = $autoloadPathNormalizer;
        $this->composerJsonFactory = $composerJsonFactory;
    }

    /**
     * @param SmartFileInfo[] $composerPackageFileInfos
     */
    public function mergeFileInfos(array $composerPackageFileInfos): ComposerJson
    {
        $mainComposerJson = new ComposerJson();

        foreach ($composerPackageFileInfos as $packageFileInfo) {
            $packageComposerJson = $this->composerJsonFactory->createFromFileInfo($packageFileInfo);

            $this->mergeJsonToRoot($mainComposerJson, $packageComposerJson, $packageFileInfo);
        }

        return $mainComposerJson;
    }

    public function mergeJsonToRoot(
        ComposerJson $rootComposerJson,
        ComposerJson $jsonToMerge,
        ?SmartFileInfo $packageFileInfo = null
    ): void {
        if ($jsonToMerge->getName()) {
            $this->mergedPackagesCollector->addPackage($jsonToMerge->getName());
        }

        $this->mergeRequire($jsonToMerge, $rootComposerJson);
        $this->mergeRequireDev($jsonToMerge, $rootComposerJson);

        // prepare paths before autolaod merging
        if ($packageFileInfo !== null) {
            $this->autoloadPathNormalizer->normalizeAutoloadPaths($jsonToMerge, $packageFileInfo);
        }

        $this->mergeAutoload($jsonToMerge, $rootComposerJson);
        $this->mergeAutoloadDev($jsonToMerge, $rootComposerJson);

        $this->mergeRepositories($jsonToMerge, $rootComposerJson);

        if ($jsonToMerge->getExtra() !== []) {
            $extra = $this->parametersMerger->mergeWithCombine($rootComposerJson->getExtra(), $jsonToMerge->getExtra());

            // do not merge extra alias as only for local packages
            if (isset($extra['branch-alias'])) {
                unset($extra['branch-alias']);
            }

            if (is_array($extra)) {
                $rootComposerJson->setExtra($extra);
            }
        }
    }

    public function mergeJsonToRootFilePathAndSave(ComposerJson $newComposerJson, ComposerJson $rootComposerJson): void
    {
        $this->mergeJsonToRoot($rootComposerJson, $newComposerJson);
    }

    private function mergeRequire(ComposerJson $jsonToMerge, ComposerJson $rootComposerJson): void
    {
        if ($jsonToMerge->getRequire() === []) {
            return;
        }
        $require = $this->parametersMerger->mergeWithCombine(
            $rootComposerJson->getRequire(),
            $jsonToMerge->getRequire()
        );
        $require = $this->arraySorter->recursiveSort($require);

        $rootComposerJson->setRequire($require);
    }

    private function mergeRequireDev(ComposerJson $jsonToMerge, ComposerJson $rootComposerJson): void
    {
        if ($jsonToMerge->getRequireDev() !== []) {
            $requireDev = $this->parametersMerger->mergeWithCombine(
                $rootComposerJson->getRequireDev(),
                $jsonToMerge->getRequireDev()
            );
            $requireDev = $this->arraySorter->recursiveSort($requireDev);

            $rootComposerJson->setRequireDev($requireDev);
        }
    }

    private function mergeAutoload(ComposerJson $jsonToMerge, ComposerJson $mainComposerJson): void
    {
        if ($jsonToMerge->getAutoload() === []) {
            return;
        }

        $autoload = $this->parametersMerger->mergeWithCombine(
            $mainComposerJson->getAutoload(),
            $jsonToMerge->getAutoload()
        );
        $autoload = $this->arraySorter->recursiveSort($autoload);

        $mainComposerJson->setAutoload($autoload);
    }

    private function mergeAutoloadDev(ComposerJson $jsonToMerge, ComposerJson $rootComposerJson): void
    {
        if ($jsonToMerge->getAutoloadDev() === []) {
            return;
        }

        $autoloadDev = $this->parametersMerger->mergeWithCombine(
            $rootComposerJson->getAutoloadDev(),
            $jsonToMerge->getAutoloadDev()
        );
        $autoloadDev = $this->arraySorter->recursiveSort($autoloadDev);

        $rootComposerJson->setAutoloadDev($autoloadDev);
    }

    private function mergeRepositories(ComposerJson $jsonToMerge, ComposerJson $rootComposerJson): void
    {
        if ($jsonToMerge->getRepositories() === []) {
            return;
        }

        $repositories = $this->parametersMerger->mergeWithCombine(
            $rootComposerJson->getRepositories(),
            $jsonToMerge->getRepositories()
        );

        $repositories = $this->arraySorter->recursiveSort($repositories);

        // uniquate special cases, ref https://github.com/symplify/symplify/issues/1197
        $repositories = array_unique($repositories, SORT_REGULAR);
        // remove keys
        $repositories = array_values($repositories);

        $rootComposerJson->setRepositories($repositories);
    }
}
