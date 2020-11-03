<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoDynamicMethodNameRule\Fixture;

final class DynamicFuncCallName
{
    public function run($value)
    {
        $value();
    }
}
