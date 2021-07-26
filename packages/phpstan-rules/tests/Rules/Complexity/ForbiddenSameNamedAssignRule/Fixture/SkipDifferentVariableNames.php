<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedAssignRule\Fixture;

final class SkipDifferentVariableNames
{
    public function run()
    {
        $first = 1000;
        $second = 10000;
    }
}
