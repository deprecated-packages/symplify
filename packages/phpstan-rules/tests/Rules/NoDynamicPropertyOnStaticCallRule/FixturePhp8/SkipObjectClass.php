<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicPropertyOnStaticCallRule\FixturePhp8;

final class SkipObjectClass
{
    public function run(SomeInvokableClass $someInvokableClass)
    {
        return $someInvokableClass::class;
    }
}
