<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoParentMethodCallOnNoOverrideProcessRule\Fixture;

final class SkipNotCallParentMethod
{
    public function foo()
    {
        static::bar();
    }

    private static function bar()
    {

    }
}
