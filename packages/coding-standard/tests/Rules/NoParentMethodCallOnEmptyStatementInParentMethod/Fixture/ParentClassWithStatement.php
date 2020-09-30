<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoParentMethodCallOnEmptyStatementInParentMethod\Fixture;

abstract class ParentClassWithStatement
{
    protected function setUp(): void
    {
        echo '';
    }

    protected function other(): void
    {
    }
}
