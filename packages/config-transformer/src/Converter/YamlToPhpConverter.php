<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Converter;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;
use Symplify\PhpConfigPrinter\Dummy\YamlContentProvider;
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
    /**
     * @var string[]
     */
    private const ROUTING_KEYS = ['resource', 'prefix', 'path', 'controller'];

    public function __construct(
        private Parser $parser,
        private PhpParserPhpConfigPrinter $phpParserPhpConfigPrinter,
        private ContainerConfiguratorReturnClosureFactory $containerConfiguratorReturnClosureFactory,
        private RoutingConfiguratorReturnClosureFactory $routingConfiguratorReturnClosureFactory,
        private YamlContentProvider $yamlContentProvider,
        private CheckerServiceParametersShifter $checkerServiceParametersShifter
    ) {
    }

    public function convert(string $yaml): string
    {
        $this->yamlContentProvider->setContent($yaml);

        /** @var mixed[]|null $yamlArray */
        $yamlArray = $this->parser->parse($yaml, Yaml::PARSE_CUSTOM_TAGS | Yaml::PARSE_CONSTANT);
        if ($yamlArray === null) {
            return '';
        }

        return $this->convertYamlArray($yamlArray);
    }

    /**
     * @param array<string, mixed> $yamlArray
     */
    public function convertYamlArray(array $yamlArray): string
    {
        dump($yamlArray);

        if ($this->isRouteYaml($yamlArray)) {
            $return = $this->routingConfiguratorReturnClosureFactory->createFromArrayData($yamlArray);
        } else {
            $yamlArray = $this->checkerServiceParametersShifter->process($yamlArray);
            $return = $this->containerConfiguratorReturnClosureFactory->createFromYamlArray($yamlArray);
        }

        return $this->phpParserPhpConfigPrinter->prettyPrintFile([$return]);
    }

    /**
     * @param array<string, mixed> $yamlLines
     */
    private function isRouteYaml(array $yamlLines): bool
    {
        foreach ($yamlLines as $yamlLine) {
            foreach (self::ROUTING_KEYS as $routeKey) {
                if (isset($yamlLine[$routeKey])) {
                    return true;
                }
            }
        }

        return false;
    }
}
