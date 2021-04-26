<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNestedFuncCallRule\Fixture;

final class NestedYourself
{
    public function run($items, $callback)
    {
        return implode('-', implode($callback, $items));
    }
}
