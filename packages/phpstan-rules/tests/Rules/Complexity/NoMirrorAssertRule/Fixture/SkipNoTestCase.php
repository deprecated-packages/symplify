<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoMirrorAssertRule\Fixture;

final class SkipNoTestCase
{
    public function test()
    {
        $this->assertSame(1, 1);
    }
}
