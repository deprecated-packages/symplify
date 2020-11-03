<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReturnArrayVariableListRule\Fixture;

final class ReturnVariables
{
    public function run($value, $value2)
    {
        return [$value, $value2];
    }
}

