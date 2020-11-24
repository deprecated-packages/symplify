<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Tests\StaticCallWithFilterReplacer;

use Iterator;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\TemplateChecker\HttpKernel\TemplateCheckerKernel;
use Symplify\TemplateChecker\StaticCallWithFilterReplacer;

final class StaticCallWithFilterReplacerTest extends AbstractKernelTestCase
{
    /**
     * @var StaticCallWithFilterReplacer
     */
    private $staticCallWithFilterReplacer;

    protected function setUp(): void
    {
        self::bootKernel(TemplateCheckerKernel::class);
        $this->staticCallWithFilterReplacer = self::$container->get(StaticCallWithFilterReplacer::class);
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

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.latte');
    }
}
