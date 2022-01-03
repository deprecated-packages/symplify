<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PrefixAbstractClassRule\Fixture;

final class SkipAnonymousClass
{
    public function testSomething(): void
    {
        $class = new class() {};
    }
}
