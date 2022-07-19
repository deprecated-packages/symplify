<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoDuplicatedVariableCasingNameRule\Fixture;

final class SkipUnitedName
{
    public function run()
    {
        $value = 1000;

        $value = 2000;
    }
}
