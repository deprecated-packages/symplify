<?php

declare(strict_types=1);

namespace Symplify\Astral\NodeFinder;

use PhpParser\Node;
use PhpParser\NodeFinder;

/**
 * @api
 * @deprecated This services does not bring any value after PHPStan 1.7 without parent nodes. Make use of NodeFinder, or hook to parent node instead.
 */
final class SimpleNodeFinder
{
    public function __construct(
        private NodeFinder $nodeFinder
    ) {
    }

    /**
     * @template T of Node
     * @param class-string<T> $nodeClass
     * @return T[]
     */
    public function findByType(Node $node, string $nodeClass): array
    {
        return $this->nodeFinder->findInstanceOf($node, $nodeClass);
    }

    /**
     * @template T of Node
     * @param array<class-string<T>> $nodeClasses
     */
    public function hasByTypes(Node $node, array $nodeClasses): bool
    {
        foreach ($nodeClasses as $nodeClass) {
            $foundNodes = $this->findByType($node, $nodeClass);
            if ($foundNodes !== []) {
                return true;
            }
        }

        return false;
    }
}
