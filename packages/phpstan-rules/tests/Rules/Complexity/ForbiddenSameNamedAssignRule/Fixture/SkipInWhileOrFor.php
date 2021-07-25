<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedAssignRule\Fixture;

final class SkipInWhileOrFor
{
    public function run()
    {
        $first = 1000;

        for ($i = 1; $i < 1000; ++$i) {
            $first = 10000;
        }
    }
}
