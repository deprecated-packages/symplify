<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\IfElseToMatchSpotterRule\Fixture;

final class EmptyArrayAssign
{
    public function run(int $count)
    {
        $result = [];

        if ($count === 1) {
            $result['test'] = 'yes';
        } elseif ($count === 0) {
            $result['test'] = 'maybe';
        } else {
            $result['test'] = 'no';
        }

        return $result;
    }
}
