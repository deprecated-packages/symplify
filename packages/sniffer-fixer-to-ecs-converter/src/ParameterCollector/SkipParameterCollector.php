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

    /**
     * @var ClassFromKeyResolver
     */
    private $classFromKeyResolver;

    public function __construct(ClassFromKeyResolver $classFromKeyResolver)
    {
        $this->classFromKeyResolver = $classFromKeyResolver;
    }

    /**
     * @return array<string, string[]|null>
     */
    public function collectSkipParameter(SimpleXMLElement $simpleXml): array
    {
        $skipParameter = [];

        foreach ($simpleXml->children() as $name => $child) {
            if (! $child instanceof SimpleXMLElement) {
                continue;
            }

            if ($name === 'rule' && (property_exists($child, 'exclude') && $child->exclude !== null)) {
                $id = (string) $child->exclude['name'];
                $className = $this->classFromKeyResolver->resolveFromStringName($id);
                $skipParameter[$className] = null;
            }

            if (! isset($child[self::REF])) {
                continue;
            }

            $ruleId = (string) $child[self::REF];
            if (! $this->isRuleStringReference($ruleId)) {
                continue;
            }

            $className = $this->classFromKeyResolver->resolveFromStringName($ruleId);

            $excludePatterns = $this->resolveExcludedPatterns($child);
            if ($excludePatterns === []) {
                continue;
            }

            /** @var string[] $uniqueClassNames */
            $uniqueClassNames = array_unique($excludePatterns);
            $skipParameter[$className] = $uniqueClassNames;
        }

        return $skipParameter;
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

        return $excludePatterns;
    }

    private function isRuleStringReference(string $ruleId): bool
    {
        return substr_count($ruleId, '.') === 2;
    }
}
