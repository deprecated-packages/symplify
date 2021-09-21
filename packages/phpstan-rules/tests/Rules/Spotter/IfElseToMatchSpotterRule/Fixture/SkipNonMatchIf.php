<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\IfElseToMatchSpotterRule\Fixture;

final class SkipNonMatchIf
{
    public function run($value)
    {
        if ($value) {
            return 100;
        }
    }
}
