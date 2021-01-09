<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;
use Symplify\PhpConfigPrinter\Contract\YamlFileContentProviderInterface;
use Symplify\PhpConfigPrinter\NodeFactory\ContainerConfiguratorReturnClosureFactory;
use Symplify\PhpConfigPrinter\NodeFactory\RoutingConfiguratorReturnClosureFactory;
use Symplify\PhpConfigPrinter\Printer\PhpParserPhpConfigPrinter;
use Symplify\PhpConfigPrinter\Yaml\CheckerServiceParametersShifter;

/**
 * @source https://raw.githubusercontent.com/archeoprog/maker-bundle/make-convert-services/src/Util/PhpServicesCreator.php
 * @see \Symplify\PhpConfigPrinter\Tests\YamlToPhpConverter\YamlToPhpConverterTest
 */
final class YamlToPhpConverter
{
    /**
     * @var string[]
     */
    private const ROUTING_KEYS = ['resource', 'prefix', 'path', 'controller'];

    /**
     * @var Parser
     */
    private $yamlParser;

    /**
     * @var PhpParserPhpConfigPrinter
     */
    private $phpParserPhpConfigPrinter;

    /**
     * @var ContainerConfiguratorReturnClosureFactory
     */
    private $containerConfiguratorReturnClosureFactory;

    /**
     * @var YamlFileContentProviderInterface
     */
    private $yamlFileContentProvider;

    /**
     * @var CheckerServiceParametersShifter
     */
    private $checkerServiceParametersShifter;

    /**
     * @var RoutingConfiguratorReturnClosureFactory
     */
    private $routingConfiguratorReturnClosureFactory;

    public function __construct(
        Parser $yamlParser,
        PhpParserPhpConfigPrinter $phpParserPhpConfigPrinter,
        ContainerConfiguratorReturnClosureFactory $returnClosureNodesFactory,
        RoutingConfiguratorReturnClosureFactory $routingConfiguratorReturnClosureFactory,
        YamlFileContentProviderInterface $yamlFileContentProvider,
        CheckerServiceParametersShifter $checkerServiceParametersShifter
    ) {
        $this->yamlParser = $yamlParser;
        $this->phpParserPhpConfigPrinter = $phpParserPhpConfigPrinter;
        $this->containerConfiguratorReturnClosureFactory = $returnClosureNodesFactory;
        $this->yamlFileContentProvider = $yamlFileContentProvider;
        $this->checkerServiceParametersShifter = $checkerServiceParametersShifter;
        $this->routingConfiguratorReturnClosureFactory = $routingConfiguratorReturnClosureFactory;
    }

    public function convert(string $yaml): string
    {
        $this->yamlFileContentProvider->setContent($yaml);

        /** @var mixed[]|null $yamlArray */
        $yamlArray = $this->yamlParser->parse($yaml, Yaml::PARSE_CUSTOM_TAGS | Yaml::PARSE_CONSTANT);
        if ($yamlArray === null) {
            return '';
        }

        return $this->convertYamlArray($yamlArray);
    }

    public function convertYamlArray(array $yamlArray): string
    {
        if ($this->isRouteYaml($yamlArray)) {
            $return = $this->routingConfiguratorReturnClosureFactory->createFromArrayData($yamlArray);
        } else {
            $yamlArray = $this->checkerServiceParametersShifter->process($yamlArray);
            $return = $this->containerConfiguratorReturnClosureFactory->createFromYamlArray($yamlArray);
        }

        return $this->phpParserPhpConfigPrinter->prettyPrintFile([$return]);
    }

    private function isRouteYaml(array $yaml): bool
    {
        foreach ($yaml as $value) {
            foreach (self::ROUTING_KEYS as $routeKey) {
                if (isset($value[$routeKey])) {
                    return true;
                }
            }
        }

        return false;
    }
}
