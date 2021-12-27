<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoReadonlyStaticVariableRule\Fixture;

final class SkipUsedStaticVariable
{
    public function run(): void
    {
        static $counter = 0;

        ++$counter;
    }
}
