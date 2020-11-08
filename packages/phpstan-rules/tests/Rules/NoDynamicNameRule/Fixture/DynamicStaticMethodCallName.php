<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicNameRule\Fixture;

final class DynamicStaticMethodCallName
{
    public function run($value)
    {
        self::$value();
    }
}
