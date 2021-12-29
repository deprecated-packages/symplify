<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoReadonlyStaticVariableRule\Fixture;

final class SkipNullAssignedStaticVariable
{
    public function run(): void
    {
        static $variable = null;
        $variable ??= 1000;
    }
}
