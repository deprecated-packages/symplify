<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNestedFuncCallRule\Fixture;

use PhpParser\Node\Expr;

final class SkipAssert
{
    public function run($items, $callback)
    {
        return assert(str_contains(Expr::class, '\\'));
    }
}
