<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeFinder;

use PhpParser\Node;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;

final class ParentNodeFinder
{
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
            if (is_a($node, $nodeClass, true) && $node instanceof Node) {
                return $node;
            }

            $node = $node->getAttribute(PHPStanAttributeKey::PARENT);
        }

        return null;
    }
}
