<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector\ClassLike;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

/**
 * @implements Collector<ClassLike, array<int, int>>>
 */
final class PropertyTypeSeaLevelCollector implements Collector
{
    public function getNodeType(): string
    {
        return ClassLike::class;
    }

    /**
     * @param ClassLike $node
     * @return array<int, int>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // return typed properties/all properties
        $propertyCount = count($node->getProperties());

        $typedPropertyCount = 0;

        foreach ($node->getProperties() as $property) {
            if ($property->type === null) {
                continue;
            }

            ++$typedPropertyCount;
        }

        return [$typedPropertyCount, $propertyCount];
    }
}
