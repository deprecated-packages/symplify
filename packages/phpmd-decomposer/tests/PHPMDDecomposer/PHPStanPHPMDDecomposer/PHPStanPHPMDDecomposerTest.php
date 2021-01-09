<?php

declare(strict_types=1);

namespace Symplify\PHPMDDecomposer\Tests\PHPMDDecomposer\PHPStanPHPMDDecomposer;

use Iterator;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\DataProvider\StaticFixtureUpdater;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\PHPMDDecomposer\HttpKernel\PHPMDDecomposerKernel;
use Symplify\PHPMDDecomposer\PHPMDDecomposer\PHPStanConfigFactory;
use Symplify\PHPMDDecomposer\Printer\PHPStanPrinter;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PHPStanPHPMDDecomposerTest extends AbstractKernelTestCase
{
    /**
     * @var PHPStanConfigFactory
     */
    private $phpStanConfigFactory;

    /**
     * @var PHPStanPrinter
     */
    private $phpStanPrinter;

    protected function setUp(): void
    {
        $this->bootKernel(PHPMDDecomposerKernel::class);
        $this->phpStanConfigFactory = $this->getService(PHPStanConfigFactory::class);
        $this->phpStanPrinter = $this->getService(PHPStanPrinter::class);
    }

    /**
     * For more on this testing workflow @see https://github.com/symplify/easy-testing
     *
     * @dataProvider provideDataForPHPStan()
     */
    public function testPHPStan(SmartFileInfo $fixtureFileInfo): void
    {
        $inputFileInfoAndExpectedFileInfo = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos(
            $fixtureFileInfo
        );

        $phpStanConfig = $this->phpStanConfigFactory->decompose(
            $inputFileInfoAndExpectedFileInfo->getInputFileInfo()
        );

        $this->assertFalse($phpStanConfig->isEmpty());

        $phpstanFileContent = $this->phpStanPrinter->printPHPStanConfig($phpStanConfig);

        // here we update test fixture if the content changed
        StaticFixtureUpdater::updateFixtureContent(
            $inputFileInfoAndExpectedFileInfo->getInputFileInfo(),
            $phpstanFileContent,
            $fixtureFileInfo
        );

        $this->assertSame(
            $inputFileInfoAndExpectedFileInfo->getExpectedFileContent(),
            $phpstanFileContent,
            $fixtureFileInfo->getRelativeFilePathFromCwd()
        );
    }

    public function provideDataForPHPStan(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.xml');
    }
}
