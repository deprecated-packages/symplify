<?php

declare(strict_types=1);

namespace Symplify\SnifferFixerToECSConverter;

use SimpleXMLElement;
use Symplify\PhpConfigPrinter\YamlToPhpConverter;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SnifferFixerToECSConverter\ParameterCollector\SkipParameterCollector;
use Symplify\SnifferFixerToECSConverter\ValueResolver\ClassFromKeyResolver;

/**
 * @see \Symplify\SnifferFixerToECSConverter\Tests\SnifferToECSConverter\SnifferToECSConverterTest
 */
final class SnifferToECSConverter
{
    /**
     * @var string
     */
    private const REF = 'ref';

    /**
     * @var YamlToPhpConverter
     */
    private $yamlToPhpConverter;

    /**
     * @var SymfonyConfigFormatFactory
     */
    private $symfonyConfigFormatFactory;

    /**
     * @var ClassFromKeyResolver
     */
    private $classFromKeyResolver;

    /**
     * @var SkipParameterCollector
     */
    private $skipParameterCollector;

    public function __construct(
        YamlToPhpConverter $yamlToPhpConverter,
        SymfonyConfigFormatFactory $symfonyConfigFormatFactory,
        ClassFromKeyResolver $classFromKeyResolver,
        SkipParameterCollector $skipParameterCollector
    ) {
        $this->yamlToPhpConverter = $yamlToPhpConverter;
        $this->symfonyConfigFormatFactory = $symfonyConfigFormatFactory;
        $this->classFromKeyResolver = $classFromKeyResolver;
        $this->skipParameterCollector = $skipParameterCollector;
    }

    public function convertFile(SmartFileInfo $phpcsFileInfo): string
    {
        $simpleXml = new SimpleXMLElement($phpcsFileInfo->getContents());

        $excludePathsParameter = [];
        $setsParameter = [];

        foreach ($simpleXml->children() as $name => $child) {
            // skip option
            if ($name === 'exclude-pattern') {
                $excludePathsParameter[] = (string) $child;
                continue;
            }

            if (! isset($child[self::REF])) {
                continue;
            }

            $ruleId = (string) $child[self::REF];
            if ($ruleId === 'PSR2') {
                $setsParameter[] = 'PSR_2';
            }
        }

        $sniffClasses = $this->collectSniffClasses($simpleXml);
        $skipParameter = $this->skipParameterCollector->collectSkipParameter($simpleXml);

        $yaml = $this->symfonyConfigFormatFactory->createSymfonyConfigFormat(
            $sniffClasses,
            $setsParameter,
            $skipParameter,
            $excludePathsParameter,
            []
        );

        return $this->yamlToPhpConverter->convertYamlArray($yaml);
    }

    /**
     * @return array<string, mixed>
     */
    private function collectSniffClasses(SimpleXMLElement $simpleXml): array
    {
        $sniffClasses = [];

        foreach ($simpleXml->children() as $child) {
            if (! isset($child[self::REF])) {
                continue;
            }

            $ruleId = (string) $child[self::REF];
            if (! $this->isRuleStringReference($ruleId)) {
                continue;
            }

            $sniffClass = $this->classFromKeyResolver->resolveFromStringName($ruleId);
            $sniffClasses[$sniffClass] = $this->resolveServiceConfiguration($child);
        }

        return $sniffClasses;
    }

    private function isRuleStringReference(string $ruleId): bool
    {
        return substr_count($ruleId, '.') === 2;
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveServiceConfiguration(SimpleXMLElement $child): array
    {
        if (! (property_exists($child, 'properties') && $child->properties !== null)) {
            return [];
        }

        $serviceConfiguration = [];

        foreach ($child->properties as $properties) {
            foreach ($properties as $property) {
                if (! $property instanceof SimpleXMLElement) {
                    continue;
                }

                $name = (string) $property->attributes()['name'];
                $serviceConfiguration[$name] = $this->resolvePropertyValue($property);
            }
        }

        return $serviceConfiguration;
    }

    /**
     * @return int|string
     */
    private function resolvePropertyValue(SimpleXMLElement $property)
    {
        $value = (string) $property->attributes()['value'];

        // retype number
        if (strlen((string) (int) $value) === strlen($value)) {
            $value = (int) $value;
        }
        return $value;
    }
}
