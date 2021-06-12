<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Merge\Package;

use Symfony\Component\Finder\Finder;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\ComposerJsonMerger;
use Symplify\MonorepoBuilder\Tests\Merge\ComposerJsonDecorator\AbstractComposerJsonDecoratorTest;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;

abstract class AbstractMergeTestCase extends AbstractComposerJsonDecoratorTest
{
    private ComposerJsonMerger $composerJsonMerger;

    private FinderSanitizer $finderSanitizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->composerJsonMerger = $this->getService(ComposerJsonMerger::class);
        $this->finderSanitizer = $this->getService(FinderSanitizer::class);
    }

    protected function doTestDirectoryMergeToFile(
        string $directoryWithJsonFiles,
        ComposerJson $expectedComposerJson
    ): void {
        $fileInfos = $this->getFileInfosFromDirectory($directoryWithJsonFiles);
        $mergedComposerJson = $this->composerJsonMerger->mergeFileInfos($fileInfos);

        $this->assertComposerJsonEquals($expectedComposerJson, $mergedComposerJson);
    }

    /**
     * @return SmartFileInfo[]
     */
    private function getFileInfosFromDirectory(string $directory): array
    {
        $finder = Finder::create()
            ->files()
            ->in($directory)
            ->name('*.json')
            ->sortByName();

        return $this->finderSanitizer->sanitize($finder);
    }
}
