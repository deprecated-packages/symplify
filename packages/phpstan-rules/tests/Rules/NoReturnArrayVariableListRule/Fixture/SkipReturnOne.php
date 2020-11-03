<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReturnArrayVariableListRule\Fixture;

final class SkipReturnOne
{
    public function run($value)
    {
        return [$value];
    }
}

