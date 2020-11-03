<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMultiArrayAssignRule\Fixture;

final class SkipDifferntArrayAssign
{
    public function run()
    {
        $values = [];
        $values['some'] = [];
        $values['another_some'] = [];
    }
}
