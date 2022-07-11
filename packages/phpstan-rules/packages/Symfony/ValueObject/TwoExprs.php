<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\ValueObject;

use PhpParser\Node\Expr;

final class TwoExprs
{
    public function __construct(
        private Expr $firstExpr,
        private Expr $secondExpr
    ) {
    }

    public function getFirstExpr(): Expr
    {
        return $this->firstExpr;
    }

    public function getSecondExpr(): Expr
    {
        return $this->secondExpr;
    }
}
