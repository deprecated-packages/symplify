<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Config\ConfigFileAnalyzer\NonExistingClassConstantExtractor;

use Iterator;
use Symplify\EasyCI\Config\ConfigFileAnalyzer\NonExistingClassConstantConfigFileAnalyzer;
use Symplify\EasyCI\Kernel\EasyCIKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class NonExistingClassConstantExtractorTest extends AbstractKernelTestCase
{
    private NonExistingClassConstantConfigFileAnalyzer $nonExistingClassConstantConfigFileAnalyzer;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->nonExistingClassConstantConfigFileAnalyzer = $this->getService(
            NonExistingClassConstantConfigFileAnalyzer::class
        );
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $filePath, int $expectedMissingCount): void
    {
        $fileInfo = new SmartFileInfo($filePath);

        $nonExistingClassConstants = $this->nonExistingClassConstantConfigFileAnalyzer->processFileInfos([$fileInfo]);
        $this->assertCount($expectedMissingCount, $nonExistingClassConstants);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/template/non_existing.latte', 1];
        yield [__DIR__ . '/Fixture/template/non_existing.twig', 1];

        yield [__DIR__ . '/Fixture/template/existing_with_number.latte', 0];
        yield [__DIR__ . '/Fixture/template/existing_with_lowercase.latte', 0];

        yield [__DIR__ . '/Fixture/template/existing.latte', 0];
        yield [__DIR__ . '/Fixture/template/existing.twig', 0];
    }
}
