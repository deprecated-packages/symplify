<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\IfElseToMatchSpotterRule\Fixture;

final class LaterReturnDefault
{
    public function run($value)
    {
        $items = [];
        if ($value === 1) {
            $items['i'] = 'yes';
        } elseif ($value === 2) {
            $items['i'] = 'maybe';
        }

        return $items;
    }
}
