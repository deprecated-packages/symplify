<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMultiArrayAssignRule\Fixture;

final class MultiArrayAssign
{
    public function run()
    {
        $values = [];
        $values['some'][1] = [];
        $values['some'][2] = [];
    }
}
