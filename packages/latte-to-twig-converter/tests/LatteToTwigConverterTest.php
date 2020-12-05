<?php

declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\Tests;

use Iterator;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\LatteToTwigConverter\HttpKernel\LatteToTwigConverterKernel;
use Symplify\LatteToTwigConverter\LatteToTwigConverter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class LatteToTwigConverterTest extends AbstractKernelTestCase
{
    /**
     * @var LatteToTwigConverter
     */
    private $LatteToTwigConverter;

    protected function setUp(): void
    {
        $this->bootKernel(LatteToTwigConverterKernel::class);
        $this->LatteToTwigConverter = $this->getService(LatteToTwigConverter::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputFileInfoAndExpectedFileInfo = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos(
            $fixtureFileInfo
        );

        $convertedContent = $this->LatteToTwigConverter->convertFile(
            $inputFileInfoAndExpectedFileInfo->getInputFileInfo()
        );

        $this->assertSame(
            $inputFileInfoAndExpectedFileInfo->getExpectedFileContent(),
            $convertedContent,
            $fixtureFileInfo->getRelativeFilePathFromCwd()
        );
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.latte');
    }
}
