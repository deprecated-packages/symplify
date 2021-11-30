<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\IfElseToMatchSpotterRule\Fixture;

final class MatchingIfCandidate
{
    public function run($value)
    {
        if ($value === 50) {
            return 100;
        } elseif ($value === 1000) {
            return 500;
        } else {
            return 1000;
        }
    }
}
