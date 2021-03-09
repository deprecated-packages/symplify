<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Propagate\Tests\VersionPropagator;

use Iterator;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\MonorepoBuilder\Merge\Tests\ComposerJsonDecorator\AbstractComposerJsonDecoratorTest;
use Symplify\MonorepoBuilder\Propagate\VersionPropagator;
use Symplify\SmartFileSystem\SmartFileInfo;

final class VersionPropagaterTest extends AbstractComposerJsonDecoratorTest
{
    /**
     * @var VersionPropagator
     */
    private $versionPropagator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->versionPropagator = $this->getService(VersionPropagator::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $trioContent = $this->trioFixtureSplitter->splitFileInfo($fixtureFileInfo);

        $mainComposerJson = $this->composerJsonFactory->createFromString($trioContent->getFirstValue());
        $packageComposerJson = $this->createComposerJson($trioContent->getSecondValue());

        $this->versionPropagator->propagate($mainComposerJson, $packageComposerJson);

        $this->assertComposerJsonEquals($trioContent->getExpectedResult(), $packageComposerJson);
    }

    /**
     * @return Iterator<mixed, SmartFileInfo>
     */
    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectoryExclusively(__DIR__ . '/Fixture', '*.json');
    }
}
