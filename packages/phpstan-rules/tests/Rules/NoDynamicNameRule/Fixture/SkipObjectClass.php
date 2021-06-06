<?php
declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicNameRule\Fixture;

final class SkipObjectClass
{
    public function run(SomeInvokableClass $someInvokableClass)
    {
        return $someInvokableClass::class;
    }
}
