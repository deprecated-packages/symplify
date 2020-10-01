<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\PreferredClassConstantOverVariableConstantRule\Fixture;

final class VariableConstant
{
    public const FOO = 'Foo';
}

function () {
    $obj = new VariableConstant();
    $obj::FOO;
};

