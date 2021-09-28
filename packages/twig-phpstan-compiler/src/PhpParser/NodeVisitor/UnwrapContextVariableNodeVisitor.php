<?php

declare(strict_types=1);

namespace Symplify\TwigPHPStanCompiler\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeVisitorAbstract;
use Symplify\Astral\Naming\SimpleNameResolver;

/**
 * Turns: $context['value'] ↓ $value
 */
final class UnwrapContextVariableNodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
    ) {
    }

    public function enterNode(Node $node): Node|null
    {
        if (! $node instanceof ArrayDimFetch) {
            return null;
        }

        if (! $this->simpleNameResolver->isName($node->var, 'context')) {
            return null;
        }

        if (! $node->dim instanceof String_) {
            return null;
        }

        $string = $node->dim;
        $stringValue = $string->value;

        // meta variable → skip
        if (str_starts_with($stringValue, '_')) {
            return null;
        }

        return new Variable($stringValue);
    }
}
