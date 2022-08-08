<?php

declare(strict_types=1);

namespace Symplify\Astral;

use PhpParser\Node;
use PhpParser\NodeFinder;

/**
 * @api
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
     * @param Node[]|Node $nodes
     * @param class-string<TNode> $type
     * @return TNode[]
     */
    public function findInstanceOf(array|Node $nodes, string $type): array
    {
        return $this->nodeFinder->findInstanceOf($nodes, $type);
    }
}
