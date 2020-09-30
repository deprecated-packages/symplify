<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoParentMethodCallOnEmptyStatementInParentMethod\Fixture;

final class CallParentMethod extends ParentClass
{
    public function setUp(): void
    {
        parent::setUp();
    }
}
