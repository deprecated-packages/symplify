<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Naming\NameNodeResolver;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use Symplify\PHPStanRules\Contract\NameNodeResolver\NameNodeResolverInterface;

final class ClassLikeNameNodeResolver implements NameNodeResolverInterface
{
    public function match(Node $node): bool
    {
        return $node instanceof ClassLike;
    }

    /**
     * @param ClassLike $node
     */
    public function resolve(Node $node): ?string
    {
        if (property_exists($node, 'namespacedName')) {
            return (string) $node->namespacedName;
        }

        if ($node->name === null) {
            return null;
        }

        return (string) $node->name;
    }
}
