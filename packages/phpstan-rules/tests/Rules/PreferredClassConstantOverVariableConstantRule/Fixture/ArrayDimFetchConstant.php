<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredClassConstantOverVariableConstantRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\PreferredClassConstantOverVariableConstantRule\Source\SomeClassWithConstant;

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
