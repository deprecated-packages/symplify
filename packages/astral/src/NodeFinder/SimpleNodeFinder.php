<?php

declare(strict_types=1);

namespace Symplify\Astral\NodeFinder;

use PhpParser\Node;
use PhpParser\NodeFinder;
use Symplify\Astral\ValueObject\AttributeKey;

/**
 * @api
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

    /**
     * @see https://phpstan.org/blog/generics-in-php-using-phpdocs for template
     *
     * @deprecated In PHPStan 1.7 were parent nodes removed, to improve performnace. This method will follow. Make use of custom node visitor or re-hook rule to the parent node directly.
     *
     * @template T of Node
     * @param class-string<T> $nodeClass
     * @return T|null
     */
    public function findFirstParentByType(Node $node, string $nodeClass): ?Node
    {
        $node = $node->getAttribute(AttributeKey::PARENT);

        while ($node instanceof Node) {
            if (is_a($node, $nodeClass, true)) {
                return $node;
            }

            $node = $node->getAttribute(AttributeKey::PARENT);
        }

        return null;
    }

    /**
     * @deprecated In PHPStan 1.7 were parent nodes removed, to improve performnace. This method will follow. Make use of custom node visitor or re-hook rule to the parent node directly.
     *
     * @template T of Node
     * @param array<class-string<T>&class-string<Node>> $nodeTypes
     * @return T|null
     */
    public function findFirstParentByTypes(Node $node, array $nodeTypes): ?Node
    {
        $node = $node->getAttribute(AttributeKey::PARENT);
        while ($node instanceof Node) {
            foreach ($nodeTypes as $nodeType) {
                if (is_a($node, $nodeType)) {
                    return $node;
                }
            }

            $node = $node->getAttribute(AttributeKey::PARENT);
        }

        return null;
    }
}
