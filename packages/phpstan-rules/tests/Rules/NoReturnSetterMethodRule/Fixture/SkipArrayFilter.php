<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReturnSetterMethodRule\Fixture;

final class SkipArrayFilter
{
    public function setItems(array $items): void
    {
        array_map(function ($item) {
            return $item;
        }, array_filter($items));
    }
}
