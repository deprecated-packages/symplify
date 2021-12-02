<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject\Spotter;

use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

final class IfAndCondExpr
{
    public function __construct(
        private Stmt $stmt,
        private Expr|null $condExpr
    ) {
    }

    public function getStmt(): Stmt
    {
        return $this->stmt;
    }

    public function getCondExpr(): ?Expr
    {
        return $this->condExpr;
    }
}
