<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireStringArgumentInMethodCallRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\RequireStringArgumentInMethodCallRule\Source\AlwaysCallMeWithString;
use Symplify\PHPStanRules\Tests\Rules\RequireStringArgumentInMethodCallRule\Source\AnotherClassWithConstant;

final class SkipWithConstant
{
    public function run(): void
    {
        new AlwaysCallMeWithString(0, AnotherClassWithConstant::CONSTANT_NAME);
    }
}
