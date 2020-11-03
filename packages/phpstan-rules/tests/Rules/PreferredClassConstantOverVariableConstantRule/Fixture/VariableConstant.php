<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredClassConstantOverVariableConstantRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\PreferredClassConstantOverVariableConstantRule\Source\SomeClassWithConstant;

final class VariableConstant
{
    public function run()
    {
        $obj = new SomeClassWithConstant();
        $obj::SOME_CONSTANT;
    }
}
