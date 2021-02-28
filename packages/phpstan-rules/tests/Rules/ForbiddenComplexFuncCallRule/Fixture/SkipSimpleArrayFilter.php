<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenComplexFuncCallRule\Fixture;

final class SkipSimpleArrayFilter
{
    public function run(array $items)
    {
        return array_filter($items, function ($item) {
            return (bool) $item;
        });
    }
}
