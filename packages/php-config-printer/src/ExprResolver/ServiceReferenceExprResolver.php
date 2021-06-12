<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ExprResolver;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name\FullyQualified;

final class ServiceReferenceExprResolver
{
    public function __construct(
        private StringExprResolver $stringExprResolver
    ) {
    }

    public function resolveServiceReferenceExpr(
        string $value,
        bool $skipServiceReference,
        string $functionName
    ): Expr {
        $value = ltrim($value, '@');
        $expr = $this->stringExprResolver->resolve($value, $skipServiceReference, false);

        if ($skipServiceReference) {
            return $expr;
        }

        $args = [new Arg($expr)];
        return new FuncCall(new FullyQualified($functionName), $args);
    }
}
