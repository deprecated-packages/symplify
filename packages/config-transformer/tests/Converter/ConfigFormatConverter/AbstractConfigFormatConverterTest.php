<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Tests\Converter\ConfigFormatConverter;

use Symplify\ConfigTransformer\Configuration\Configuration;
use Symplify\ConfigTransformer\Converter\ConfigFormatConverter;
use Symplify\ConfigTransformer\HttpKernel\ConfigTransformerKernel;
use Symplify\EasyTesting\DataProvider\StaticFixtureUpdater;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

abstract class AbstractConfigFormatConverterTest extends AbstractKernelTestCase
{
    /**
     * @var ConfigFormatConverter
     */
    protected $configFormatConverter;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var SmartFileSystem
     */
    protected $smartFileSystem;

    protected function setUp(): void
    {
        $this->bootKernel(ConfigTransformerKernel::class);

        $this->configFormatConverter = $this->getService(ConfigFormatConverter::class);
        $this->smartFileSystem = $this->getService(SmartFileSystem::class);
        $this->configuration = $this->getService(Configuration::class);
    }

    protected function doTestOutput(SmartFileInfo $fixtureFileInfo): void
    {
        $inputAndExpected = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos($fixtureFileInfo);

        $this->doTestFileInfo(
            $inputAndExpected->getInputFileInfo(),
            $inputAndExpected->getExpectedFileContent(),
            $fixtureFileInfo
        );
    }

    protected function doTestFileInfo(
        SmartFileInfo $inputFileInfo,
        string $expectedContent,
        SmartFileInfo $fixtureFileInfo
    ): void {
        $convertedContent = $this->configFormatConverter->convert($inputFileInfo);
        StaticFixtureUpdater::updateFixtureContent($inputFileInfo, $convertedContent, $fixtureFileInfo);

        $this->assertSame($expectedContent, $convertedContent, $fixtureFileInfo->getRelativeFilePathFromCwd());
    }
}
