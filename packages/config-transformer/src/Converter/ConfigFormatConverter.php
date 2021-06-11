<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Converter;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\YamlDumper;
use Symfony\Component\Yaml\Yaml;
use Symplify\ConfigTransformer\Collector\XmlImportCollector;
use Symplify\ConfigTransformer\ConfigLoader;
use Symplify\ConfigTransformer\DependencyInjection\ContainerBuilderCleaner;
use Symplify\ConfigTransformer\ValueObject\Format;
use Symplify\PackageBuilder\Exception\NotImplementedYetException;
use Symplify\PhpConfigPrinter\Provider\CurrentFilePathProvider;
use Symplify\PhpConfigPrinter\YamlToPhpConverter;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class ConfigFormatConverter
{
    public function __construct(
        private ConfigLoader $configLoader,
        private YamlToPhpConverter $yamlToPhpConverter,
        private CurrentFilePathProvider $currentFilePathProvider,
        private XmlImportCollector $xmlImportCollector,
        private ContainerBuilderCleaner $containerBuilderCleaner
    ) {
    }

    public function convert(SmartFileInfo $smartFileInfo): string
    {
        $this->currentFilePathProvider->setFilePath($smartFileInfo->getRealPath());

        $containerBuilderAndFileContent = $this->configLoader->createAndLoadContainerBuilderFromFileInfo(
            $smartFileInfo
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

        $content = $yamlDumper->dump();
        if (! is_string($content)) {
            throw new ShouldNotHappenException();
        }

        return $content;
    }

    private function decorateWithCollectedXmlImports(string $dumpedYaml): string
    {
        $collectedXmlImports = $this->xmlImportCollector->provide();
        if ($collectedXmlImports === []) {
            return $dumpedYaml;
        }

        $yamlArray = Yaml::parse($dumpedYaml, Yaml::PARSE_CUSTOM_TAGS);
        $yamlArray['imports'] = array_merge($yamlArray['imports'] ?? [], $collectedXmlImports);

        return Yaml::dump($yamlArray, 10, 4, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
    }
}
