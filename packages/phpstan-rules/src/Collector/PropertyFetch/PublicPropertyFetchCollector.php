<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector\PropertyFetch;

use PhpParser\Node\Expr\Variable;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Type\TypeWithClassName;

/**
 * @implements Collector<PropertyFetch, string[]>
 */
final class PublicPropertyFetchCollector implements Collector
{
<<<<<<< HEAD
=======
    /**
     * @return class-string<Node>
     */
>>>>>>> 73b9ce0fa ([ci-review] Rector Rectify)
    public function getNodeType(): string
    {
        return Node\Expr\PropertyFetch::class;
    }

    /**
     * @param Node\Expr\PropertyFetch $node
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
