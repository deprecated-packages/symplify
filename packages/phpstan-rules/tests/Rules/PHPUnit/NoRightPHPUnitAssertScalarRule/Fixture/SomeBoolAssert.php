<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoRightPHPUnitAssertScalarRule\Fixture;

use PHPUnit\Framework\TestCase;

final class SomeBoolAssert extends TestCase
{
    public function test()
    {
        $value = 1000;
        $this->assertSame($value, false);
    }
}
