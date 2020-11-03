<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\PreferredClassConstantOverVariableConstantRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\PreferredClassConstantOverVariableConstantRule\Source\SomeClassWithConstant;

final class VariableConstant
{
    public function run()
    {
        $obj = new SomeClassWithConstant();
        $obj::SOME_CONSTANT;
    }
}
