<?php

declare(strict_types=1);

namespace Symplify\SnifferFixerToECSConverter\Tests\FixerToECSConverter;

use Iterator;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\DataProvider\StaticFixtureUpdater;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;
use Symplify\SnifferFixerToECSConverter\FixerToECSConverter;
use Symplify\SnifferFixerToECSConverter\HttpKernel\SnifferFixerToECSKernel;

final class FixerToECSConverterTest extends AbstractKernelTestCase
{
    /**
     * @var FixerToECSConverter
     */
    private $fixerToECSConverter;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    protected function setUp(): void
    {
        $this->bootKernel(SnifferFixerToECSKernel::class);
        $this->fixerToECSConverter = self::$container->get(FixerToECSConverter::class);
        $this->smartFileSystem = self::$container->get(SmartFileSystem::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        // add local "packages" directory, to make config run happy
        $packagesDirectory = StaticFixtureSplitter::getTemporaryPath() . '/temporary-packages';
        $this->smartFileSystem->dumpFile($packagesDirectory . '/some_file.txt', 'some content');

        $inputAndExpectedFileInfo = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos(
            $fixtureFileInfo
        );

        $convertedContent = $this->fixerToECSConverter->convertFile($inputAndExpectedFileInfo->getInputFileInfo());

        StaticFixtureUpdater::updateFixtureContent(
            $inputAndExpectedFileInfo->getInputFileInfo(),
            $convertedContent,
            $fixtureFileInfo
        );

        $this->assertStringMatchesFormat(
            $inputAndExpectedFileInfo->getExpectedFileContent(),
            $convertedContent,
            $fixtureFileInfo->getRelativeFilePathFromCwd()
        );
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.dist');
    }
}
