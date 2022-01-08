<?php

declare(strict_types=1);

namespace Symplify\Astral\NodeFinder;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeFinder;
use Symplify\Astral\ValueObject\AttributeKey;
use Symplify\PackageBuilder\Php\TypeChecker;

final class SimpleNodeFinder
{
    public function __construct(
        private TypeChecker $typeChecker,
        private NodeFinder $nodeFinder
    ) {
    }

    /**
     * @template T of Node
     * @param class-string<T> $nodeClass
     * @return T|null
     */
    public function findFirstByType(Node $node, string $nodeClass): Node|null
    {
        return $this->nodeFinder->findFirstInstanceOf($node, $nodeClass);
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

    /**
     * @see https://phpstan.org/blog/generics-in-php-using-phpdocs for template
     *
     * @template T of Node
     * @param class-string<T> $nodeClass
     * @return T|null
     */
    public function findFirstParentByType(Node $node, string $nodeClass): ?Node
    {
        $node = $node->getAttribute(AttributeKey::PARENT);
        while ($node) {
            if (is_a($node, $nodeClass, true)) {
                return $node;
            }

            $node = $node->getAttribute(AttributeKey::PARENT);
        }

        return null;
    }

    /**
     * @template T of Node
     * @param array<class-string<T>&class-string<Node>> $nodeTypes
     * @return T|null
     */
    public function findFirstParentByTypes(Node $node, array $nodeTypes): ?Node
    {
        $node = $node->getAttribute(AttributeKey::PARENT);
        while ($node) {
            if ($this->typeChecker->isInstanceOf($node, $nodeTypes)) {
                return $node;
            }

            $node = $node->getAttribute(AttributeKey::PARENT);
        }

        return null;
    }

    /**
     * @param Node|Node[] $nodes
     * @param callable(Node $node): bool $filter
     */
    public function findFirst(Node | array $nodes, callable $filter): ?Node
    {
        return $this->nodeFinder->findFirst($nodes, $filter);
    }

    /**
     * @param callable(Node $node): bool $filter
     */
    public function findFirstPrevious(Node $node, callable $filter): ?Node
    {
        $node = $node instanceof Expression ? $node : $node->getAttribute(AttributeKey::CURRENT_NODE);
        if ($node === null) {
            return null;
        }

        $foundNode = $this->findFirst([$node], $filter);
        // we found what we need
        if ($foundNode !== null) {
            return $foundNode;
        }

        // move to previous expression
        $previousStatement = $node->getAttribute(AttributeKey::PREVIOUS);
        if ($previousStatement !== null) {
            return $this->findFirstPrevious($previousStatement, $filter);
        }

        $parent = $node->getAttribute(AttributeKey::PARENT);
        if ($parent === null) {
            return null;
        }

        return $this->findFirstPrevious($parent, $filter);
    }

    /**
     * @param callable(Node $node):bool $filter
     */
    public function findFirstPreviousOfNode(Node $node, callable $filter): ?Node
    {
        // move to previous expression
        $previousStatement = $node->getAttribute(AttributeKey::PREVIOUS);
        if ($previousStatement !== null) {
            $foundNode = $this->findFirst([$previousStatement], $filter);
            // we found what we need
            if ($foundNode !== null) {
                return $foundNode;
            }

            return $this->findFirstPreviousOfNode($previousStatement, $filter);
        }

        $parent = $node->getAttribute(AttributeKey::PARENT);
        if ($parent instanceof FunctionLike) {
            return null;
        }

        if ($parent instanceof Node) {
            return $this->findFirstPreviousOfNode($parent, $filter);
        }

        return null;
    }
}
