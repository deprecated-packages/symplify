<?php

declare(strict_types=1);

namespace Symplify\TwigPHPStanCompiler\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\NodeVisitorAbstract;
use Symplify\Astral\Naming\SimpleNameResolver;

final class UnwrapTwigEnsureTraversableNodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
    ) {
    }

    public function enterNode(Node $node): Node|null
    {
        if (! $node instanceof FuncCall) {
            return null;
        }

        if (! $this->simpleNameResolver->isName($node, 'twig_ensure_traversable')) {
            return null;
        }

        $firstArg = $node->args[0];

        return $firstArg->value;
    }
}
