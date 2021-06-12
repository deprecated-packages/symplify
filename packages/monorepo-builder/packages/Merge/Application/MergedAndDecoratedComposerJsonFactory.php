<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Application;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\ComposerJsonMerger;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerJsonDecoratorInterface;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\MonorepoBuilder\Tests\Merge\Application\MergedAndDecoratedComposerJsonFactoryTest
 */
final class MergedAndDecoratedComposerJsonFactory
{
    /**
     * @param ComposerJsonDecoratorInterface[] $composerJsonDecorators
     */
    public function __construct(
        private ComposerJsonMerger $composerJsonMerger,
        private array $composerJsonDecorators
    ) {
    }

    /**
     * @param SmartFileInfo[] $packageFileInfos
     */
    public function createFromRootConfigAndPackageFileInfos(
        ComposerJson $mainComposerJson,
        array $packageFileInfos
    ): void {
        $mergedAndDecoratedComposerJson = $this->mergePackageFileInfosAndDecorate($packageFileInfos);

        $this->composerJsonMerger->mergeJsonToRoot($mainComposerJson, $mergedAndDecoratedComposerJson);
    }

    /**
     * @param SmartFileInfo[] $packageFileInfos
     */
    private function mergePackageFileInfosAndDecorate(array $packageFileInfos): ComposerJson
    {
        $mergedComposerJson = $this->composerJsonMerger->mergeFileInfos($packageFileInfos);
        foreach ($this->composerJsonDecorators as $composerJsonDecorator) {
            $composerJsonDecorator->decorate($mergedComposerJson);
        }

        return $mergedComposerJson;
    }
}
