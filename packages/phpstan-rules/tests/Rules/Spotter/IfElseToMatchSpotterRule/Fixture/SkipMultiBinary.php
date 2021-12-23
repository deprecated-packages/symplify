<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\IfElseToMatchSpotterRule\Fixture;

final class SkipMultiBinary
{
    public function run($value, $items)
    {
        if ($items && ($value === 10)) {
            $items[] = 111;
        }

        return $items;
    }
}
