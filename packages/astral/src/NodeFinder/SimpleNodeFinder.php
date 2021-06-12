<?php

declare(strict_types=1);

namespace Symplify\Astral\NodeFinder;

use PhpParser\Node;
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
            if ($this->findByType($node, $nodeClass)) {
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
     * @param class-string<T>[] $nodeTypes
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
}
