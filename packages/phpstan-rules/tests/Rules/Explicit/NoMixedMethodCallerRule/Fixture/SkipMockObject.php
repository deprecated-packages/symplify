<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedMethodCallerRule\Fixture;

use PHPUnit\Framework\TestCase;

final class SkipMockObject extends TestCase
{
    public function test()
    {
        $mock = $this->createMock(MagicMethodName::class);

        $mock
            ->method('run')
            ->willReturn(true);
    }
}
