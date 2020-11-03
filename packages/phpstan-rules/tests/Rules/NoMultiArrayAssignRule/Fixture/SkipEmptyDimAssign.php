<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMultiArrayAssignRule\Fixture;

final class SkipEmptyDimAssign
{
    public function run()
    {
        $values = [];
        $values[] = [];
        $values[] = [];
    }
}
