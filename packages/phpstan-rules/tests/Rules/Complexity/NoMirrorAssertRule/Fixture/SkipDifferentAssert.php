<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoMirrorAssertRule\Fixture;

use PHPUnit\Framework\TestCase;

final class SkipDifferentAssert extends TestCase
{
    public function test()
    {
        $value = 1000;
        $this->assertSame(1, $value);
    }
}
