<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredClassConstantOverVariableConstantRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\PreferredClassConstantOverVariableConstantRule\Source\SomeClassWithConstant;

final class SkipClassContantFetch
{
    public function run()
    {
        return SomeClassWithConstant::SOME_CONSTANT;
    }
}
