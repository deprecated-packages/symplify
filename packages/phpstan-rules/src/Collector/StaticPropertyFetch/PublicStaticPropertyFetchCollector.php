<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector\StaticPropertyFetch;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

/**
 * @implements Collector<StaticPropertyFetch, string[]>
 * @deprecated
 */
final class PublicStaticPropertyFetchCollector implements Collector
{
    public function getNodeType(): string
    {
        return StaticPropertyFetch::class;
    }

    /**
     * @param StaticPropertyFetch $node
     * @return string[]|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if (! $node->class instanceof Name) {
            return null;
        }

        if (! $node->name instanceof Identifier) {
            return null;
        }

        if ($node->class->toString() === 'self') {
            // self fetch is allowed
            return null;
        }

        $className = $node->class->toString();
        $propertyName = $node->name->toString();

        return [$className . '::' . $propertyName];
    }
}
