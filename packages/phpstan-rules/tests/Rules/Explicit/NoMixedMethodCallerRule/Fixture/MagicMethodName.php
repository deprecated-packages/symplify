<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedMethodCallerRule\Fixture;

final class MagicMethodName
{
    public function run($someType, $magic)
    {
        $someType->$magic();
    }
}
