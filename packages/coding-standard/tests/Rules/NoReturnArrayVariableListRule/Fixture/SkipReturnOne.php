<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoReturnArrayVariableListRule\Fixture;

final class SkipReturnOne
{
    public function run($value)
    {
        return [$value];
    }
}

