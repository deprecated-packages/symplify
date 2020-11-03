<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnEmptyStatementInParentMethodRule\Fixture;

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
