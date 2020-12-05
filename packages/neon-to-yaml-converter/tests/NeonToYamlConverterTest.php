<?php

declare(strict_types=1);

namespace Symplify\NeonToYamlConverter\Tests;

use Iterator;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\NeonToYamlConverter\ArrayParameterCollector;
use Symplify\NeonToYamlConverter\HttpKernel\NeonToYamlKernel;
use Symplify\NeonToYamlConverter\NeonToYamlConverter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class NeonToYamlConverterTest extends AbstractKernelTestCase
{
    /**
     * @var NeonToYamlConverter
     */
    private $neonToYamlConverter;

    /**
     * @var ArrayParameterCollector
     */
    private $arrayParameterCollector;

    protected function setUp(): void
    {
        $this->bootKernel(NeonToYamlKernel::class);

        $this->neonToYamlConverter = $this->getService(NeonToYamlConverter::class);
        $this->arrayParameterCollector = $this->getService(ArrayParameterCollector::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputAndExpected = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos($fixtureFileInfo);
        $this->arrayParameterCollector->collectFromFiles([$inputAndExpected->getInputFileInfo()]);

        $convertedFileContent = $this->neonToYamlConverter->convertFileInfo($inputAndExpected->getInputFileInfo());

        $this->assertSame(
            $inputAndExpected->getExpectedFileContent(),
            $convertedFileContent,
            $fixtureFileInfo->getRelativeFilePathFromCwd()
        );
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.neon');
    }
}
