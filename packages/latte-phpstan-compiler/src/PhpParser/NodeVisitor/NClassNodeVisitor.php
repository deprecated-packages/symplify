<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Echo_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitorAbstract;

/**
 * Make ($ʟ_tmp = \array_filter(['class1', $var ? 'class2' : \null])) ? ' class="' . \Latte\Runtime\Filters::escapeHtmlAttr(\implode(" ", \array_unique($ʟ_tmp))) . '"' : "";
 *
 * to: ' class="' . \Latte\Runtime\Filters::escapeHtmlAttr(\implode(" ", \array_unique(\array_filter(['class1', $var ? 'class2' : \null])))) . '"';
 */
final class NClassNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @return Node[]|null
     */
    public function leaveNode(Node $node): array|null
    {
        if (! $node instanceof Echo_) {
            return null;
        }

        if (! isset($node->exprs[0])) {
            return null;
        }

        if (! $node->exprs[0] instanceof Ternary) {
            return null;
        }

        $ternary = $node->exprs[0];

        // looking for `class="' . \Latte\Runtime\Filters::escapeHtmlAttr()`
        if (! $ternary->if instanceof Concat) {
            return null;
        }

        if (! $ternary->if->left instanceof Concat) {
            return null;
        }

        if (! $ternary->if->left->left instanceof String_) {
            return null;
        }
        $left = $ternary->if->left->left;
        if ($left->value !== ' class="') {
            return null;
        }

        return [
            new Expression($ternary->cond, $node->getAttributes()),
            new Echo_([
                new Concat(new Concat($left, $ternary->if->left->right), $ternary->if->right),
            ]),
        ];
    }
}
