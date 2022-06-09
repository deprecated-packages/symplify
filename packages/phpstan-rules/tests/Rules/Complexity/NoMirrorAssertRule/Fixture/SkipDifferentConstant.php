<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoMirrorAssertRule\Fixture;

use PHPUnit\Framework\TestCase;

final class SkipDifferentConstant extends TestCase
{
    private const LEFT = 'left';

    private const RIGHT = 'right';

    public function test()
    {
        $this->assertSame(self::LEFT, self::RIGHT);
    }
}
