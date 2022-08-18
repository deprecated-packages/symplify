<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Config\ConfigFileAnalyzer\NonExistingClassConfigFileAnalyzer;

use Iterator;
use Symplify\EasyCI\Config\ConfigFileAnalyzer\NonExistingClassConfigFileAnalyzer;
use Symplify\EasyCI\Kernel\EasyCIKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class NonExistingClassConfigFileAnalyzerTest extends AbstractKernelTestCase
{
    private NonExistingClassConfigFileAnalyzer $nonExistingClassConfigFileAnalyzer;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->nonExistingClassConfigFileAnalyzer = $this->getService(NonExistingClassConfigFileAnalyzer::class);

        require_once __DIR__ . '/Source/LowercaseFactory.php';
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $filePath, int $expectedClassCount): void
    {
        $fileInfo = new SmartFileInfo($filePath);

        $nonExistingClasses = $this->nonExistingClassConfigFileAnalyzer->processFileInfos([$fileInfo]);
        $this->assertCount($expectedClassCount, $nonExistingClasses);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/config/skip_psr4_autodiscovery.yaml', 0];

        // templates
        yield [__DIR__ . '/Fixture/template/file_with_existing_class.twig', 0];
        yield [__DIR__ . '/Fixture/template/different_file.twig', 1];

        // blade, laravel
        yield [__DIR__ . '/Fixture/template/non_existing_in_blade_file.php', 3];
        yield [__DIR__ . '/Fixture/template/existing_in_blade_file.php', 0];
    }
}
