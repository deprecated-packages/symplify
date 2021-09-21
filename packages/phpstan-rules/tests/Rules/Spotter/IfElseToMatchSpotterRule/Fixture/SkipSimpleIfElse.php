<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\IfElseToMatchSpotterRule\Fixture;

final class SkipSimpleIfElse
{
    public function run($value)
    {
        if ($value = 100) {
            return 100;
        } else {
            return 1000;
        }
    }
}
