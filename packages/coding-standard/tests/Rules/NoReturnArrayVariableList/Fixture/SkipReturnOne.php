<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoReturnArrayVariableList\Fixture;

final class SkipReturnOne
{
    public function run($value)
    {
        return [$value];
    }
}

