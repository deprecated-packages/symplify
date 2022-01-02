<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\SwitchToMatchSpotterRule\Fixture;

final class SimpleSwitch
{
    public function run($values)
    {
        switch ($values) {
            case 1:
                return 10;
            case 2:
                return 20;
            default:
                return 30;
        }
    }
}
