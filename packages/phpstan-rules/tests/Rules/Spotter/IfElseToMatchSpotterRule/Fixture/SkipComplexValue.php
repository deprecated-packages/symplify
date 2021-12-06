<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\IfElseToMatchSpotterRule\Fixture;

final class SkipComplexValue
{
    private $user;

    public function run($alert, $id)
    {
        if ($id === 150) {
            $defaults[$alert] = 1;
        } elseif ($this->user * 5 === 250) {
            $defaults[$alert] = 2;
        } else {
            $defaults[$alert] = 0;
        }

        return $defaults;
    }
}
