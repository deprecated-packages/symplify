<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Package;

use Symplify\MonorepoBuilder\ComposerJsonMerger;
use Symplify\MonorepoBuilder\ComposerJsonObject\ComposerJsonFactory;
use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Configuration\MergedPackagesCollector;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @deprecated Use
 * @see \Symplify\MonorepoBuilder\ComposerJsonMerger
 */
final class PackageComposerJsonMerger
{
    /**
     * @var MergedPackagesCollector
     */
    private $mergedPackagesCollector;

    /**
     * @var ComposerJsonFactory
     */
    private $composerJsonFactory;

    /**
     * @var ComposerJsonMerger
     */
    private $composerJsonMerger;

    public function __construct(
        MergedPackagesCollector $mergedPackagesCollector,
        ComposerJsonFactory $composerJsonFactory,
        ComposerJsonMerger $composerJsonMerger
    ) {
        $this->mergedPackagesCollector = $mergedPackagesCollector;
        $this->composerJsonFactory = $composerJsonFactory;
        $this->composerJsonMerger = $composerJsonMerger;
    }

    /**
     * @param SmartFileInfo[] $composerPackageFileInfos
     */
    public function mergeFileInfos(array $composerPackageFileInfos): ComposerJson
    {
        $mainComposerJson = new ComposerJson();

        foreach ($composerPackageFileInfos as $packageFileInfo) {
            $packageComposerJson = $this->composerJsonFactory->createFromFileInfo($packageFileInfo);

            if ($packageComposerJson->getName()) {
                $this->mergedPackagesCollector->addPackage($packageComposerJson->getName());
            }

            $this->composerJsonMerger->mergeJsonToRoot($packageComposerJson, $mainComposerJson, $packageFileInfo);
        }

        $this->filterOutDuplicatesRequireAndRequireDev($mainComposerJson);

        return $mainComposerJson;
    }

    private function filterOutDuplicatesRequireAndRequireDev(ComposerJson $composerJson): void
    {
        if ($composerJson->getRequire() === [] || $composerJson->getRequireDev() === []) {
            return;
        }

        $duplicatedPackages = array_intersect(
            array_keys($composerJson->getRequire()),
            array_keys($composerJson->getRequireDev())
        );

        $currentRequireDev = $composerJson->getRequireDev();

        foreach (array_keys($currentRequireDev) as $package) {
            if (in_array($package, $duplicatedPackages, true)) {
                unset($currentRequireDev[$package]);
            }
        }

        $composerJson->setRequireDev($currentRequireDev);
    }
}
