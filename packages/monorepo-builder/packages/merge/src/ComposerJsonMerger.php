<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge;

use Symplify\MonorepoBuilder\ComposerJsonObject\ComposerJsonFactory;
use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Configuration\MergedPackagesCollector;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;
use Symplify\MonorepoBuilder\Merge\PathResolver\AutoloadPathNormalizer;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ComposerJsonMerger
{
    /**
     * @var ComposerKeyMergerInterface[]
     */
    private $composerKeyMergers = [];

    /**
     * @var MergedPackagesCollector
     */
    private $mergedPackagesCollector;

    /**
     * @var AutoloadPathNormalizer
     */
    private $autoloadPathNormalizer;

    /**
     * @var ComposerJsonFactory
     */
    private $composerJsonFactory;

    /**
     * @param ComposerKeyMergerInterface[] $composerKeyMergers
     */
    public function __construct(
        ComposerJsonFactory $composerJsonFactory,
        MergedPackagesCollector $mergedPackagesCollector,
        AutoloadPathNormalizer $autoloadPathNormalizer,
        array $composerKeyMergers
    ) {
        $this->mergedPackagesCollector = $mergedPackagesCollector;
        $this->autoloadPathNormalizer = $autoloadPathNormalizer;
        $this->composerJsonFactory = $composerJsonFactory;
        $this->composerKeyMergers = $composerKeyMergers;
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

        // prepare paths before autolaod merging
        if ($packageFileInfo !== null) {
            $this->autoloadPathNormalizer->normalizeAutoloadPaths($jsonToMerge, $packageFileInfo);
        }

        foreach ($this->composerKeyMergers as $composerKeyMerger) {
            $composerKeyMerger->merge($rootComposerJson, $jsonToMerge);
        }
    }
}
