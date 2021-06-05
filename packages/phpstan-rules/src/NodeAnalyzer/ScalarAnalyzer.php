<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt\Return_;

final class ScalarAnalyzer
{
    public function isScalarReturn(Node $node): bool
    {
        if (! $node instanceof Return_) {
            return false;
        }

        if ($node->expr === null) {
            return false;
        }

        return $this->isScalar($node->expr);
    }

    public function isScalar(Expr $expr): bool
    {
        if ($expr instanceof Instanceof_) {
            return true;
        }

        if ($expr instanceof Scalar) {
            return true;
        }

        if ($expr instanceof Array_) {
            return $expr->items !== [];
        }

        return false;
    }
}
