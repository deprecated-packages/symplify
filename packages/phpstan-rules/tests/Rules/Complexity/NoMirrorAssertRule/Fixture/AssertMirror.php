<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoMirrorAssertRule\Fixture;

use PHPUnit\Framework\TestCase;

final class AssertMirror extends TestCase
{
    public function test()
    {
        $this->assertSame(1, 1);
    }
}
