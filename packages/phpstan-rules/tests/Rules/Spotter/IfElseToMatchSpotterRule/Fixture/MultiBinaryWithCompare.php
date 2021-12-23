<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\IfElseToMatchSpotterRule\Fixture;

final class MultiBinaryWithCompare
{
    public function run($value)
    {
        $items = [];

        if ($value === 50 || $value === 10) {
            $items[] = 111;
        }

        return $items;
    }
}
