<?php

declare(strict_types=1);

namespace Symplify\Astral\NodeNameResolver;

use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use Symplify\Astral\Contract\NodeNameResolverInterface;

final class ConstFetchNodeNameResolver implements NodeNameResolverInterface
{
    public function match(Node $node): bool
    {
        return $node instanceof ConstFetch;
    }

    /**
     * @param ConstFetch $node
     */
    public function resolve(Node $node): ?string
    {
        return $node->name->toString();
    }
}
