<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedAssignRule\Fixture;

final class SkipPositionNames
{
    public function run()
    {
        $position = 100;

        $position = $position + 10;

        // ...

        $position = $position + 10;


        $position = 100 + 10;


        $position = 100 + 10;
    }

    public function runAgain()
    {
        $currentPosition = 100;

        $currentPosition = $currentPosition + 10;

        // ...

        $currentPosition = $currentPosition + 10;


        $currentPosition = 100 + 10;


        $currentPosition = 100 + 10;
    }
}
