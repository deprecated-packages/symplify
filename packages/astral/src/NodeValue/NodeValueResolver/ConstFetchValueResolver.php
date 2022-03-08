<?php

declare(strict_types=1);

namespace Symplify\Astral\NodeValue\NodeValueResolver;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ConstFetch;
use Symplify\Astral\Contract\NodeValueResolver\NodeValueResolverInterface;
use Symplify\Astral\Naming\SimpleNameResolver;

/**
 * @see \Symplify\Astral\Tests\NodeValue\NodeValueResolverTest
 *
 * @implements NodeValueResolverInterface<ConstFetch>
 */
final class ConstFetchValueResolver implements NodeValueResolverInterface
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    public function getType(): string
    {
        return ConstFetch::class;
    }

    /**
     * @param ConstFetch $expr
     */
    public function resolve(Expr $expr, string $currentFilePath): mixed
    {
        $constFetchName = $this->simpleNameResolver->getName($expr);
        if ($constFetchName === null) {
            return null;
        }

        return constant($constFetchName);
    }
}
