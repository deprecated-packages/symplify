<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoParentMethodCallOnEmptyStatementInParentMethod\Fixture;

abstract class ParentClass
{
    protected function setUp(): void
    {
        // empty statement
    }

    protected function other(): void
    {
    }
}
