<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicMethodNameRule\Fixture;

final class DynamicStaticMethodCallName
{
    public function run($value)
    {
        self::$value();
    }
}
