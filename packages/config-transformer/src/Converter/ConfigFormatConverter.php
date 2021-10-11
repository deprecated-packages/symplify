<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Converter;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\YamlDumper;
use Symfony\Component\Yaml\Yaml;
use Symplify\ConfigTransformer\Collector\XmlImportCollector;
use Symplify\ConfigTransformer\ConfigLoader;
use Symplify\ConfigTransformer\DependencyInjection\ContainerBuilderCleaner;
use Symplify\ConfigTransformer\Exception\NotImplementedYetException;
use Symplify\ConfigTransformer\ValueObject\Configuration;
use Symplify\ConfigTransformer\ValueObject\Format;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Symplify\PackageBuilder\Yaml\ParametersMerger;
use Symplify\PhpConfigPrinter\Provider\CurrentFilePathProvider;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class ConfigFormatConverter
{
    public function __construct(
        private ConfigLoader $configLoader,
        private YamlToPhpConverter $yamlToPhpConverter,
        private CurrentFilePathProvider $currentFilePathProvider,
        private XmlImportCollector $xmlImportCollector,
        private ContainerBuilderCleaner $containerBuilderCleaner,
        private PrivatesAccessor $privatesAccessor,
        private ParametersMerger $parametersMerger
    ) {
    }

    public function convert(SmartFileInfo $smartFileInfo, Configuration $configuration): string
    {
        $this->currentFilePathProvider->setFilePath($smartFileInfo->getRealPath());

        $containerBuilderAndFileContent = $this->configLoader->createAndLoadContainerBuilderFromFileInfo(
            $smartFileInfo,
            $configuration
        );

        $containerBuilder = $containerBuilderAndFileContent->getContainerBuilder();

        if ($smartFileInfo->getSuffix() === Format::YAML) {
            $dumpedYaml = $containerBuilderAndFileContent->getFileContent();
            $dumpedYaml = $this->decorateWithCollectedXmlImports($dumpedYaml);

            return $this->yamlToPhpConverter->convert($dumpedYaml);
        }

        if ($smartFileInfo->getSuffix() === Format::XML) {
            $dumpedYaml = $this->dumpContainerBuilderToYaml($containerBuilder);
            $dumpedYaml = $this->decorateWithCollectedXmlImports($dumpedYaml);

            return $this->yamlToPhpConverter->convert($dumpedYaml);
        }

        $message = sprintf('Suffix "%s" is not support yet', $smartFileInfo->getSuffix());
        throw new NotImplementedYetException($message);
    }

    private function dumpContainerBuilderToYaml(ContainerBuilder $containerBuilder): string
    {
        $yamlDumper = new YamlDumper($containerBuilder);
        $this->containerBuilderCleaner->cleanContainerBuilder($containerBuilder);

        // 1. services and parameters
        $content = $yamlDumper->dump();
        if (! is_string($content)) {
            throw new ShouldNotHappenException();
        }

        // 2. append extension yaml too
        $extensionsConfigs = $this->privatesAccessor->getPrivateProperty($containerBuilder, 'extensionConfigs');
        foreach ($extensionsConfigs as $namespace => $configs) {
            $mergedConfig = [];
            foreach ($configs as $config) {
                $mergedConfig = $this->parametersMerger->merge($mergedConfig, $config);
            }

            $extensionsConfigs[$namespace] = $mergedConfig;
        }

        $extensionsContent = $this->dumpYaml($extensionsConfigs);

        return $content . PHP_EOL . $extensionsContent;
    }

    private function decorateWithCollectedXmlImports(string $dumpedYaml): string
    {
        $collectedXmlImports = $this->xmlImportCollector->provide();
        if ($collectedXmlImports === []) {
            return $dumpedYaml;
        }

        $yamlArray = Yaml::parse($dumpedYaml, Yaml::PARSE_CUSTOM_TAGS);
        $yamlArray['imports'] = array_merge($yamlArray['imports'] ?? [], $collectedXmlImports);

        return $this->dumpYaml($yamlArray);
    }

    /**
     * @param array<string, mixed> $yamlArray
     */
    private function dumpYaml(array $yamlArray): string
    {
        if ($yamlArray === []) {
            return '';
        }

        return Yaml::dump($yamlArray, 10, 4, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
    }
}
