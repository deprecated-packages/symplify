<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use Symplify\Astral\Naming\SimpleNameResolver;

final class InitializedExprAnalyzer
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    public function isInitializationExpr(Expr $expr): bool
    {
        if ($expr instanceof Array_ && $expr->items === []) {
            return true;
        }

        if ($expr instanceof ConstFetch && $this->simpleNameResolver->isNames($expr, ['true', 'false', 'null'])) {
            return true;
        }

        if ($expr instanceof String_ && $expr->value === '') {
            return true;
        }

        return $expr instanceof LNumber && $expr->value === 0;
    }
}
