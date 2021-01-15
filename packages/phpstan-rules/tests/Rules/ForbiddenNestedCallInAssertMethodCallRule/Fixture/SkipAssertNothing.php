<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNestedCallInAssertMethodCallRule\Fixture;

final class SkipAssertNothing
{
    public function test()
    {
        $this->assertNothing();
    }
}
