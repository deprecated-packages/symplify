<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeFinder;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\NodeFinder;

/**
 * @todo remove after https://github.com/nikic/PHP-Parser/pull/869 is released
 */
final class TypeAwareNodeFinder
{
    public function __construct(
        private NodeFinder $nodeFinder
    ) {
    }

    /**
     * @template TNode as Node
     *
     * @param Node[]|Node $nodes
     * @param class-string<TNode> $type
     * @return TNode|null
     */
    public function findFirstInstanceOf(array|Node $nodes, string $type): ?Node
    {
        return $this->nodeFinder->findFirstInstanceOf($nodes, $type);
    }

    /**
     * @template TNode as Node
     *
     * @param class-string<TNode> $type
     * @return TNode[]
     */
    public function findInstanceOf(Expr $expr, string $type): array
    {
        return $this->nodeFinder->findInstanceOf($expr, $type);
    }
}
