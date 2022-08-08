<?php

declare(strict_types=1);

namespace Symplify\Astral\NodeValue\NodeValueResolver;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use Symplify\Astral\Contract\NodeValueResolver\NodeValueResolverInterface;

/**
 * @see \Symplify\Astral\Tests\NodeValue\NodeValueResolverTest
 *
 * @implements NodeValueResolverInterface<ConstFetch>
 */
final class ConstFetchValueResolver implements NodeValueResolverInterface
{
    public function getType(): string
    {
        return ConstFetch::class;
    }

    /**
     * @param ConstFetch $expr
     */
    public function resolve(Expr $expr, string $currentFilePath): mixed
    {
        if (! $expr->name instanceof Name) {
            return null;
        }

        $constFetchName = $expr->name->toString();
        return constant($constFetchName);
    }
}
