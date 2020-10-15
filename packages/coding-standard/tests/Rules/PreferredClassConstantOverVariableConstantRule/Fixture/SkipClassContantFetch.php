<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\PreferredClassConstantOverVariableConstantRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\PreferredClassConstantOverVariableConstantRule\Source\SomeClassWithConstant;

final class SkipClassContantFetch
{
    public function run()
    {
        return SomeClassWithConstant::SOME_CONSTANT;
    }
}
