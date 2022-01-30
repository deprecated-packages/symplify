<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedMethodCallerRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedMethodCallerRule\Source\KnownType;

final class SkipKnownCallerType
{
    public function run(KnownType $knownType)
    {
        $knownType->call();
    }
}
