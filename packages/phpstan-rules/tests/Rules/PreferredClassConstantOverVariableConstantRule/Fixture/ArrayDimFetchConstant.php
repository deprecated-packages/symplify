<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\PreferredClassConstantOverVariableConstantRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\PreferredClassConstantOverVariableConstantRule\Source\SomeClassWithConstant;

final class ArrayDimFetchConstant
{
    public function run()
    {
        $objs = [
            new SomeClassWithConstant()
        ];

        $objs[0]::SOME_CONSTANT;
    }
}
