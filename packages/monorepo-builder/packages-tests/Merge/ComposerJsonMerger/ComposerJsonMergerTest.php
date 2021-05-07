<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Merge\ComposerJsonMerger;

use Iterator;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\MonorepoBuilder\Merge\ComposerJsonMerger;
use Symplify\MonorepoBuilder\Tests\Merge\ComposerJsonDecorator\AbstractComposerJsonDecoratorTest;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ComposerJsonMergerTest extends AbstractComposerJsonDecoratorTest
{
    /**
     * @var ComposerJsonMerger
     */
    private $composerJsonMerger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->composerJsonMerger = $this->getService(ComposerJsonMerger::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function testFixture(SmartFileInfo $fixtureFileInfo): void
    {
        $trioContent = $this->trioFixtureSplitter->splitFileInfo($fixtureFileInfo);

        $mainComposerJson = $this->createComposerJson($trioContent->getFirstValue());
        $packageComposerJson = $this->createComposerJson($trioContent->getSecondValue());

        $this->composerJsonMerger->mergeJsonToRoot($mainComposerJson, $packageComposerJson);

        $this->assertComposerJsonEquals($trioContent->getExpectedResult(), $mainComposerJson);
    }

    /**
     * @return Iterator<mixed, SmartFileInfo>
     */
    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectoryExclusively(__DIR__ . '/Fixture', '*.json');
    }
}
