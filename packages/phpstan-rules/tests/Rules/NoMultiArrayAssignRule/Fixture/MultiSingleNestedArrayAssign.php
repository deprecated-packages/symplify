<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMultiArrayAssignRule\Fixture;

final class MultiSingleNestedArrayAssign
{
    public function run()
    {
        $values = [];
        $values['some'] = [];
        $values['some'] = [];
    }
}
