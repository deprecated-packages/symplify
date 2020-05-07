<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoDynamicMethodNameRule\Fixture;

final class DynamicStaticMethodCallName
{
    public function run($value)
    {
        self::$value();
    }
}
