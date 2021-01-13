<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNestedCallInAssertMethodCallRule\Fixture;

final class SkipCleanAssert
{
    public function test()
    {
        $values = '...';
        $this->assertSame('...', $values);
    }
}
