<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoParentMethodCallOnEmptyStatementInParentMethod\Fixture;

final class NotCallParentMethod
{
    public function foo()
    {
        static::bar();
    }

    private static function bar()
    {

    }
}
