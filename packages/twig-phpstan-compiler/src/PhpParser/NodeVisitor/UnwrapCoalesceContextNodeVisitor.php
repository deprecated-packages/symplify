<?php

declare(strict_types=1);

namespace Symplify\TwigPHPStanCompiler\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\NodeVisitorAbstract;
use Symplify\Astral\Naming\SimpleNameResolver;

final class UnwrapCoalesceContextNodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
    ) {
    }

    public function enterNode(Node $node): Node|null
    {
        if (! $node instanceof Coalesce) {
            return null;
        }

        // only variable and array dim fetches
        if (! $node->left instanceof Variable && ! $node->left instanceof ArrayDimFetch) {
            return null;
        }

        if (! $node->right instanceof ConstFetch) {
            return null;
        }

        if (! $this->simpleNameResolver->isName($node->right, 'null')) {
            return null;
        }

        return $node->left;
    }
}
