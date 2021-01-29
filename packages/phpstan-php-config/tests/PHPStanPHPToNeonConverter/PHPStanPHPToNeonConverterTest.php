<?php

declare(strict_types=1);

namespace Symplify\PHPStanPHPConfig\Tests\PHPStanPHPToNeonConverter;

use Iterator;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\PHPStanPHPConfig\HttpKernel\PHPStanPHPConfigKernel;
use Symplify\PHPStanPHPConfig\PHPStanPHPToNeonConverter;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class PHPStanPHPToNeonConverterTest extends AbstractKernelTestCase
{
    /**
     * @var PHPStanPHPToNeonConverter
     */
    private $phpStanPHPToNeonConverter;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    protected function setUp(): void
    {
        $this->bootKernel(PHPStanPHPConfigKernel::class);
        $this->phpStanPHPToNeonConverter = $this->getService(PHPStanPHPToNeonConverter::class);
        $this->smartFileSystem = $this->getService(SmartFileSystem::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        // for path check
        $temporaryPath = StaticFixtureSplitter::getTemporaryPath();
        $this->smartFileSystem->mkdir($temporaryPath . '/existing_path');
        $this->smartFileSystem->copy(
            __DIR__ . '/Fixture/import/config/rules.neon',
            $temporaryPath . '/config/rules.neon'
        );

        $splitFileInfoToLocalInputAndExpected = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpected(
            $fixtureFileInfo
        );

        $convertedContent = $this->phpStanPHPToNeonConverter->convert(
            $splitFileInfoToLocalInputAndExpected->getInputFileInfo()
        );
        $this->assertSame($splitFileInfoToLocalInputAndExpected->getExpected(), $convertedContent);
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectoryExclusively(__DIR__ . '/Fixture', '*.php');
    }
}
