<?php

declare(strict_types=1);

namespace Symplify\SnifferFixerToECSConverter\ParameterCollector;

use SimpleXMLElement;
use Symplify\SnifferFixerToECSConverter\ValueResolver\ClassFromKeyResolver;

final class SkipParameterCollector
{
    /**
     * @var string
     */
    private const REF = 'ref';

    public function __construct(
        private ClassFromKeyResolver $classFromKeyResolver
    ) {
    }

    /**
     * @return array<string|int, mixed>
     */
    public function collectSkipParameter(SimpleXMLElement $simpleXml): array
    {
        $skippedClassParameter = $this->resolveSkippedClassParameter($simpleXml);
        $skippedClassByPathsParameter = $this->resolveSkippedClassByPathsParameter($simpleXml);

        return array_merge($skippedClassParameter, $skippedClassByPathsParameter);
    }

    /**
     * @return string[]
     */
    private function resolveExcludedPatterns(SimpleXMLElement $child): array
    {
        $excludePatterns = [];
        foreach ($child->children() as $childKey => $childValue) {
            if ($childKey !== 'exclude-pattern') {
                continue;
            }

            $excludePatterns[] = (string) $childValue;
        }

        return array_unique($excludePatterns);
    }

    private function isRuleStringReference(string $ruleId): bool
    {
        return substr_count($ruleId, '.') === 2;
    }

    /**
     * @return array<string|int, string>
     */
    private function resolveSkippedClassParameter(SimpleXMLElement $simpleXml): array
    {
        $skipClasses = [];

        foreach ($simpleXml->children() as $name => $child) {
            if (! $child instanceof SimpleXMLElement) {
                continue;
            }

            if ($name === 'rule' && (property_exists($child, 'exclude') && $child->exclude !== null)) {
                $id = (string) $child->exclude['name'];
                $skipClasses[] = $this->classFromKeyResolver->resolveFromStringName($id);
            }
        }

        return $skipClasses;
    }

    /**
     * @return array<string, string[]>
     */
    private function resolveSkippedClassByPathsParameter(SimpleXMLElement $simpleXml): array
    {
        $skipParameter = [];

        foreach ($simpleXml->children() as $child) {
            if (! $child instanceof SimpleXMLElement) {
                continue;
            }

            if (! isset($child[self::REF])) {
                continue;
            }

            $ruleId = (string) $child[self::REF];
            if (! $this->isRuleStringReference($ruleId)) {
                continue;
            }

            $excludePatterns = $this->resolveExcludedPatterns($child);
            if ($excludePatterns === []) {
                continue;
            }

            $className = $this->classFromKeyResolver->resolveFromStringName($ruleId);
            $skipParameter[$className] = $excludePatterns;
        }

        return $skipParameter;
    }
}
