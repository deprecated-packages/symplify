<?php

declare(strict_types=1);

namespace Symplify\LatteToTwig\Tests;

use Iterator;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\LatteToTwig\HttpKernel\LatteToTwigKernel;
use Symplify\LatteToTwig\LatteToTwigConverter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class LatteToTwigConverterTest extends AbstractKernelTestCase
{
    /**
     * @var LatteToTwigConverter
     */
    private $latteToTwigConverter;

    protected function setUp(): void
    {
        $this->bootKernel(LatteToTwigKernel::class);
        $this->latteToTwigConverter = self::$container->get(LatteToTwigConverter::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputFileInfoAndExpectedFileInfo = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos(
            $fixtureFileInfo
        );

        $convertedContent = $this->latteToTwigConverter->convertFile(
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
