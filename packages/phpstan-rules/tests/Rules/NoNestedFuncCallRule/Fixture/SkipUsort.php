<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNestedFuncCallRule\Fixture;

final class SkipUsort
{
    public function run(array $items)
    {
        return usort($items, function ($firstName, $secondName) {
            return strcmp($firstName, $secondName);
        });
    }
}
