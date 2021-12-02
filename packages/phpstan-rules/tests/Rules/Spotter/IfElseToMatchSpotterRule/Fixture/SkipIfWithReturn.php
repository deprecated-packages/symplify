<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\IfElseToMatchSpotterRule\Fixture;

final class SkipIfWithReturn
{
    public function run($value)
    {
        $default = 100;
        if ($value === 5) {
            $default = 1000;
        }

        return 1000;
    }
}
