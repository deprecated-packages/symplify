<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoParentMethodCallOnEmptyStatementInParentMethod\Fixture;

final class CallParentMethodWithStatement extends ParentClassWithStatement
{
    public function setUp(): void
    {
        parent::setUp();
    }
}
