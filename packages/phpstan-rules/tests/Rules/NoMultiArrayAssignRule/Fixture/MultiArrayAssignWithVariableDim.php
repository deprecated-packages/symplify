<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMultiArrayAssignRule\Fixture;

final class MultiArrayAssignWithVariableDim
{
    public function run($key)
    {
        $values = [];
        $values[$key][1] = [];
        $values[$key][2] = [];
    }
}
