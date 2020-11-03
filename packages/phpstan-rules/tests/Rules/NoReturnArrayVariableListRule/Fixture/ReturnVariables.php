<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoReturnArrayVariableListRule\Fixture;

final class ReturnVariables
{
    public function run($value, $value2)
    {
        return [$value, $value2];
    }
}

