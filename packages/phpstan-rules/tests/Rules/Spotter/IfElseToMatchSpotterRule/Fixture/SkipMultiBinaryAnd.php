<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\IfElseToMatchSpotterRule\Fixture;

final class SkipMultiBinaryAnd
{
    public function run($value, $items)
    {
        if ($items === 50 && $value === 10) {
            $items[] = 111;
        }

        return $items;
    }
}
