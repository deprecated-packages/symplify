<?php

declare(strict_types=1);

namespace Symplify\Astral\NodeNameResolver;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use Symplify\Astral\Contract\NodeNameResolverInterface;

final class ClassMethodNodeNameResolver implements NodeNameResolverInterface
{
    public function match(Node $node): bool
    {
        return $node instanceof ClassMethod;
    }

    /**
     * @param ClassMethod $node
     */
    public function resolve(Node $node): ?string
    {
        return $node->name->toString();
    }
}
