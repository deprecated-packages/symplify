<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoMultiArrayAssignRule\Fixture;

final class SkipEmptyDimAssign
{
    public function run()
    {
        $values = [];
        $values[] = [];
        $values[] = [];
    }
}
