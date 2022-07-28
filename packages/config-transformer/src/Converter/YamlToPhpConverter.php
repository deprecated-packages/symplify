<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Converter;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;
use Symplify\PhpConfigPrinter\NodeFactory\ContainerConfiguratorReturnClosureFactory;
use Symplify\PhpConfigPrinter\NodeFactory\RoutingConfiguratorReturnClosureFactory;
use Symplify\PhpConfigPrinter\Printer\PhpParserPhpConfigPrinter;
use Symplify\PhpConfigPrinter\Yaml\CheckerServiceParametersShifter;

/**
 * @api
 * @source https://raw.githubusercontent.com/archeoprog/maker-bundle/make-convert-services/src/Util/PhpServicesCreator.php
 *
 * @see \Symplify\ConfigTransformer\Tests\Converter\YamlToPhpConverter\YamlToPhpConverterTest
 */
final class YamlToPhpConverter
{
    public function __construct(
        private Parser $parser,
        private PhpParserPhpConfigPrinter $phpParserPhpConfigPrinter,
        private ContainerConfiguratorReturnClosureFactory $containerConfiguratorReturnClosureFactory,
        private RoutingConfiguratorReturnClosureFactory $routingConfiguratorReturnClosureFactory,
        private CheckerServiceParametersShifter $checkerServiceParametersShifter
    ) {
    }

    public function convert(string $yaml, string $filePath): string
    {
        /** @var mixed[]|null $yamlArray */
        $yamlArray = $this->parser->parse($yaml, Yaml::PARSE_CUSTOM_TAGS | Yaml::PARSE_CONSTANT);
        if ($yamlArray === null) {
            return '';
        }

        return $this->convertYamlArray($yamlArray, $filePath);
    }

    /**
     * @param array<string, mixed> $yamlArray
     */
    public function convertYamlArray(array $yamlArray, string $filePath): string
    {
        if ($this->isRouteYaml($filePath)) {
            $return = $this->routingConfiguratorReturnClosureFactory->createFromArrayData($yamlArray);
        } else {
            $yamlArray = $this->checkerServiceParametersShifter->process($yamlArray);
            $return = $this->containerConfiguratorReturnClosureFactory->createFromYamlArray($yamlArray);
        }

        return $this->phpParserPhpConfigPrinter->prettyPrintFile([$return]);
    }

    private function isRouteYaml(string $filePath): bool
    {
        // if the paths contains this keyword, we assume it contains routes
        return !str_contains($filePath, DIRECTORY_SEPARATOR . 'packages' . DIRECTORY_SEPARATOR) && (str_contains($filePath, 'routing') || str_contains($filePath, 'routes'));
    }
}
