<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicPropertyOnStaticCallRule\Fixture;

final class SkipObjectClass
{
    public function run(SomeInvokableClass $someInvokableClass)
    {
        return $someInvokableClass::class;
    }
}
