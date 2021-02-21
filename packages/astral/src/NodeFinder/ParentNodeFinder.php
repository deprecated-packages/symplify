<?php

declare(strict_types=1);

namespace Symplify\Astral\NodeFinder;

use PhpParser\Node;
use Symplify\Astral\ValueObject\CommonAttributeKey;
use Symplify\PackageBuilder\Php\TypeChecker;

final class ParentNodeFinder
{
    /**
     * @var TypeChecker
     */
    private $typeChecker;

    public function __construct(TypeChecker $typeChecker)
    {
        $this->typeChecker = $typeChecker;
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
        $node = $node->getAttribute(CommonAttributeKey::PARENT);
        while ($node) {
            if (is_a($node, $nodeClass, true)) {
                return $node;
            }

            $node = $node->getAttribute(CommonAttributeKey::PARENT);
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
        $node = $node->getAttribute(CommonAttributeKey::PARENT);
        while ($node) {
            if ($this->typeChecker->isInstanceOf($node, $nodeTypes)) {
                return $node;
            }

            $node = $node->getAttribute(CommonAttributeKey::PARENT);
        }

        return null;
    }
}
