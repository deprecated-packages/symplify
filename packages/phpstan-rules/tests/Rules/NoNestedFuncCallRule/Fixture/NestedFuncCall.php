<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNestedFuncCallRule\Fixture;

final class NestedFuncCall
{
    public function run($items, $callback)
    {
        return array_filter(array_map($callback, $items));
    }
}
