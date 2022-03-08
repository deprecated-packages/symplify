<?php

declare(strict_types=1);

namespace Symplify\Astral\Contract\NodeValueResolver;

use PhpParser\Node\Expr;

/**
 * @template TExpr as Expr
 */
interface NodeValueResolverInterface
{
    /**
     * @return class-string<TExpr>
     */
    public function getType(): string;

    /**
     * @param TExpr $expr
     */
    public function resolve(Expr $expr, string $currentFilePath): mixed;
}
