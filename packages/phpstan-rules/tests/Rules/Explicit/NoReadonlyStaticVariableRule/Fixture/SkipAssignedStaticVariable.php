<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoReadonlyStaticVariableRule\Fixture;

final class SkipAssignedStaticVariable
{
    public function run(): void
    {
        static $variable = [];
        if ($variable === []) {
            $variable = 1000;
        }
    }
}
