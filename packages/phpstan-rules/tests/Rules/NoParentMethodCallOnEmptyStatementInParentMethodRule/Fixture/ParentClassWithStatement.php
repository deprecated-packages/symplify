<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnEmptyStatementInParentMethodRule\Fixture;

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
