<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\StaticCallWithFilterReplacer;

use Iterator;
use Symplify\EasyCI\HttpKernel\EasyCIKernel;
use Symplify\EasyCI\StaticCallWithFilterReplacer;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class StaticCallWithFilterReplacerTest extends AbstractKernelTestCase
{
    /**
     * @var StaticCallWithFilterReplacer
     */
    private $staticCallWithFilterReplacer;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->staticCallWithFilterReplacer = $this->getService(StaticCallWithFilterReplacer::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputFileInfoAndExpectedFileInfo = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos(
            $fixtureFileInfo
        );

        $inputFileInfo = $inputFileInfoAndExpectedFileInfo->getInputFileInfo();
        $changedContent = $this->staticCallWithFilterReplacer->processFileInfo($inputFileInfo);

        $expectedFileInfo = $inputFileInfoAndExpectedFileInfo->getExpectedFileInfo();
        $this->assertStringEqualsFile($expectedFileInfo->getPathname(), $changedContent);
    }

    /**
     * @return Iterator<mixed, SmartFileInfo[]>
     */
    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.latte');
    }
}
