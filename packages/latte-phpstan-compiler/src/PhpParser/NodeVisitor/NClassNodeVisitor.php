<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeVisitorAbstract;

/**
 * from: <code> echo ($ʟ_tmp = \array_filter(['class1', $var ? 'class2' : \null])) ? ' class="' .
 * \Latte\Runtime\Filters::escapeHtmlAttr(\implode(" ", \array_unique($ʟ_tmp))) . '"' : ""; </code>
 *
 * to: <code> echo ' class="' . \implode(" ", ['class1', $var ? 'class2' : \null]) . '"'; </code>
 */
final class NClassNodeVisitor extends NodeVisitorAbstract
{
    public function enterNode(Node $node): Node|null
    {
        if (! $node instanceof Ternary) {
            return null;
        }

        // looking for `class="' . \Latte\Runtime\Filters::escapeHtmlAttr()`
        if (! $node->if instanceof Concat) {
            return null;
        }

        if (! $node->if->left instanceof Concat) {
            return null;
        }

        if (! $node->if->left->left instanceof String_) {
            return null;
        }
        $left = $node->if->left->left;
        if ($left->value !== ' class="') {
            return null;
        }

        if (! $node->cond instanceof Assign) {
            return null;
        }

        if (! $node->cond->expr instanceof FuncCall) {
            return null;
        }

        /** @var FuncCall $funcCall */
        $funcCall = $node->cond->expr;

        if (! isset($funcCall->args[0])) {
            return null;
        }

        $implodeSeparatorString = new String_(' ');

        $args = [new Arg($implodeSeparatorString), $funcCall->args[0]];

        $implode = new FuncCall(new FullyQualified('implode'), $args);

        return new Concat(new Concat($left, $implode), $node->if->right);
    }
}
