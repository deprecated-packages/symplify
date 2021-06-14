<?php

declare(strict_types=1);

namespace Symplify\SnifferFixerToECSConverter\Tests\SnifferToECSConverter;

use Iterator;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\DataProvider\StaticFixtureUpdater;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SnifferFixerToECSConverter\HttpKernel\SnifferFixerToECSKernel;
use Symplify\SnifferFixerToECSConverter\SnifferToECSConverter;

final class SnifferToECSConverterTest extends AbstractKernelTestCase
{
    private SnifferToECSConverter $snifferToECSConverter;

    protected function setUp(): void
    {
        $this->bootKernel(SnifferFixerToECSKernel::class);
        $this->snifferToECSConverter = $this->getService(SnifferToECSConverter::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputAndExpectedFileInfo = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos(
            $fixtureFileInfo
        );

        $convertedContent = $this->snifferToECSConverter->convertFile($inputAndExpectedFileInfo->getInputFileInfo());

        StaticFixtureUpdater::updateFixtureContent(
            $inputAndExpectedFileInfo->getInputFileInfo(),
            $convertedContent,
            $fixtureFileInfo
        );

        $this->assertSame(
            $inputAndExpectedFileInfo->getExpectedFileContent(),
            $convertedContent,
            $fixtureFileInfo->getRelativeFilePathFromCwd()
        );
    }

    /**
     * @return Iterator<mixed, SmartFileInfo[]>
     */
    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.xml');
    }
}
