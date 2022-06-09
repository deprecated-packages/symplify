<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoMirrorAssertRule\Fixture;

use PHPUnit\Framework\TestCase;

final class SameConstant extends TestCase
{
    private const LEFT = 'left';

    public function test()
    {
        $this->assertSame(self::LEFT, self::LEFT);
    }
}
