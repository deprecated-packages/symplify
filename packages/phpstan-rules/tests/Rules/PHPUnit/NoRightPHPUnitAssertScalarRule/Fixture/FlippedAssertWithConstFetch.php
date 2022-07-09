<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoRightPHPUnitAssertScalarRule\Fixture;

use PHPUnit\Framework\TestCase;

final class FlippedAssertWithConstFetch extends TestCase
{
    public const SOME_VALUE = 10;

    public function test()
    {
        $value = 1000;

        $this->assertSame($value, self::SOME_VALUE);
    }
}
