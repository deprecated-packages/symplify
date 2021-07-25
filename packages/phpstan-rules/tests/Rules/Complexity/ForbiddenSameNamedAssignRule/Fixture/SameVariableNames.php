<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedAssignRule\Fixture;

final class SameVariableNames
{
    public function run()
    {
        $first = 1000;
        $first = 10000;
    }
}
