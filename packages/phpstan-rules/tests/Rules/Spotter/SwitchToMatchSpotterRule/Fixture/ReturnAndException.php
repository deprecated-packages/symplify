<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\SwitchToMatchSpotterRule\Fixture;

final class ReturnAndException
{
    public function run($values)
    {
        switch ($values) {
            case 1:
                return 10;
            default:
                throw new \InvalidArgumentException();
        }
    }
}
