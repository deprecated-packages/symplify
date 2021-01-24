<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNestedFuncCallRule\Fixture;

final class SkipNonNested
{
    public function run($items, $callback)
    {
        $mappedItems = array_map($items, $callback);
        return array_filter($mappedItems);
    }
}
