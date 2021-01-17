<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeFinder;

use PhpParser\Node;
use Symplify\PackageBuilder\Php\TypeChecker;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;

/**
 * @see https://phpstan.org/blog/generics-in-php-using-phpdocs for template
 */
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
    public function getFirstParentByType(Node $node, string $nodeClass): ?Node
    {
        $node = $node->getAttribute(PHPStanAttributeKey::PARENT);
        while ($node) {
            if (is_a($node, $nodeClass, true)) {
                return $node;
            }

            $node = $node->getAttribute(PHPStanAttributeKey::PARENT);
        }

        return null;
    }

    /**
     * @template T of Node
     * @param class-string<T>[] $nodeTypes
     * @return T|null
     */
    public function getFirstParentByTypes(Node $node, array $nodeTypes): ?Node
    {
        $node = $node->getAttribute(PHPStanAttributeKey::PARENT);
        while ($node) {
            if ($this->typeChecker->isInstanceOf($node, $nodeTypes)) {
                return $node;
            }

            $node = $node->getAttribute(PHPStanAttributeKey::PARENT);
        }

        return null;
    }
}
