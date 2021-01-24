<?php
declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicNameRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoDynamicNameRule\Source\SomeInvokableClass;

final class SkipInvokable
{
    public function run(SomeInvokableClass $someInvokableClass)
    {
        return $someInvokableClass(100);
    }
}
