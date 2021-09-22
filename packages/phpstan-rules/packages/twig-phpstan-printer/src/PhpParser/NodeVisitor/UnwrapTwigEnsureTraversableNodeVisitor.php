<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\TwigPHPStanPrinter\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
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
        if (! $firstArg instanceof Arg) {
            return null;
        }

        if (! $firstArg->value instanceof Coalesce) {
            return null;
        }

        $coalesce = $firstArg->value;
        if (! $coalesce->left instanceof ArrayDimFetch) {
            return null;
        }

        $arrayDimFetch = $coalesce->left;
        if (! $arrayDimFetch->dim instanceof String_) {
            return null;
        }

        $variableName = $arrayDimFetch->dim->value;
        return new Variable($variableName);
    }
}
