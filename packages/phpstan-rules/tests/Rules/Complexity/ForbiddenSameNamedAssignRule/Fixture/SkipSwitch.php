<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedAssignRule\Fixture;

final class SkipSwitch
{
    public function run($value)
    {
        switch ($value) {
            case 1:
                $start = 1;
            case 2:
                $start = 2;
        }
    }
}
