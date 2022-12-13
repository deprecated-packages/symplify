<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector\PropertyFetch;

use PhpParser\Node;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Type\TypeWithClassName;

/**
 * @implements Collector<PropertyFetch, string[]>
 */
final class PublicPropertyFetchCollector implements Collector
{
    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return PropertyFetch::class;
    }

    /**
     * @param PropertyFetch $node
     * @return string[]|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if (! $node->var instanceof Variable) {
            return null;
        }

        // skip local
        if ($node->var->name === 'this') {
            return null;
        }

        if (! $node->name instanceof Identifier) {
            return null;
        }

        $propertyFetcherType = $scope->getType($node->var);
        if (! $propertyFetcherType instanceof TypeWithClassName) {
            return null;
        }

        $className = $propertyFetcherType->getClassName();
        $propertyName = $node->name->toString();

        return [$className . '::' . $propertyName];
    }
}
