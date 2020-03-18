<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Tests\Package;

use Symfony\Component\Finder\Finder;
use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\ComposerJsonMerger;
use Symplify\MonorepoBuilder\Merge\Tests\ComposerJsonDecorator\AbstractComposerJsonDecoratorTest;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;

abstract class AbstractMergeTestCase extends AbstractComposerJsonDecoratorTest
{
    /**
     * @var ComposerJsonMerger
     */
    private $composerJsonMerger;

    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->composerJsonMerger = self::$container->get(ComposerJsonMerger::class);
        $this->finderSanitizer = self::$container->get(FinderSanitizer::class);
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
    protected function getFileInfosFromDirectory(string $directory): array
    {
        $finder = Finder::create()->files()
            ->in($directory)
            ->name('*.json')
            ->sortByName();

        return $this->finderSanitizer->sanitize($finder);
    }
}
