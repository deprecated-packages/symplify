<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedMethodCallerRule\Fixture;

final class UnknownCallerType
{
    public function run($knownType)
    {
        $knownType->call();
    }
}
