<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Tests\Converter\ConfigFormatConverter;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symplify\ConfigTransformer\Configuration\Configuration;
use Symplify\ConfigTransformer\Converter\ConfigFormatConverter;
use Symplify\ConfigTransformer\DependencyInjection\ContainerBuilderCleaner;
use Symplify\ConfigTransformer\HttpKernel\ConfigTransformerKernel;
use Symplify\ConfigTransformer\ValueObject\Format;
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

    /**
     * @var ContainerBuilderCleaner
     */
    private $containerBuilderCleaner;

    protected function setUp(): void
    {
        $this->bootKernel(ConfigTransformerKernel::class);

        $this->configFormatConverter = self::$container->get(ConfigFormatConverter::class);
        $this->containerBuilderCleaner = self::$container->get(ContainerBuilderCleaner::class);
        $this->smartFileSystem = self::$container->get(SmartFileSystem::class);
        $this->configuration = self::$container->get(Configuration::class);
    }

    protected function doTestOutput(SmartFileInfo $fixtureFileInfo, string $inputFormat, string $outputFormat): void
    {
        $this->configuration->changeInputFormat($inputFormat);
        $this->configuration->changeOutputFormat($outputFormat);

        $inputAndExpected = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos($fixtureFileInfo);

        $this->doTestFileInfo(
            $inputAndExpected->getInputFileInfo(),
            $inputAndExpected->getExpectedFileContent(),
            $fixtureFileInfo,
            $inputFormat,
            $outputFormat
        );
    }

    protected function doTestYamlContentIsLoadable(string $yamlContent): void
    {
        $localFile = sys_get_temp_dir() . '/_migrify_temporary_yaml/some_file.yaml';
        $this->smartFileSystem->dumpFile($localFile, $yamlContent);

        $containerBuilder = new ContainerBuilder();
        $yamlFileLoader = new YamlFileLoader($containerBuilder, new FileLocator());
        $yamlFileLoader->load($localFile);

        $this->containerBuilderCleaner->cleanContainerBuilder($containerBuilder);

        // at least 1 service is registered
        $definitionCount = count($containerBuilder->getDefinitions());
        $this->assertGreaterThanOrEqual(1, $definitionCount);
    }

    protected function doTestFileInfo(
        SmartFileInfo $inputFileInfo,
        string $expectedContent,
        SmartFileInfo $fixtureFileInfo,
        string $inputFormat,
        string $outputFormat
    ): void {
        $convertedContent = $this->configFormatConverter->convert($inputFileInfo, $inputFormat, $outputFormat);

        StaticFixtureUpdater::updateFixtureContent($inputFileInfo, $convertedContent, $fixtureFileInfo);

        $this->assertSame($expectedContent, $convertedContent, $fixtureFileInfo->getRelativeFilePathFromCwd());

        if ($outputFormat === Format::YAML) {
            $this->doTestYamlContentIsLoadable($convertedContent);
        }
    }
}
