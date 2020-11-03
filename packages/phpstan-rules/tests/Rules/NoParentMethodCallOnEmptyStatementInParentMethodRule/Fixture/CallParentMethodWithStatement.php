<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnEmptyStatementInParentMethodRule\Fixture;

final class CallParentMethodWithStatement extends ParentClassWithStatement
{
    public function setUp(): void
    {
        parent::setUp();
    }
}
