<?php

declare(strict_types=1);

namespace Symplify\Astral\NodeNameResolver;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use Symplify\Astral\Contract\NodeNameResolverInterface;

final class FuncCallNodeNameResolver implements NodeNameResolverInterface
{
    public function match(Node $node): bool
    {
        return $node instanceof FuncCall;
    }

    /**
     * @param FuncCall $node
     */
    public function resolve(Node $node): ?string
    {
        if ($node->name instanceof Expr) {
            return null;
        }

        return (string) $node->name;
    }
}
