<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnNoOverrideProcessRule\Fixture;

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
