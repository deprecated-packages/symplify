<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireStringArgumentInConstructorRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\RequireStringArgumentInConstructorRule\Source\AlwaysCallMeWithString;
use Symplify\PHPStanRules\Tests\Rules\RequireStringArgumentInConstructorRule\Source\AnotherClassWithConstant;

final class SkipWithConstant
{
    public function run(): void
    {
        new AlwaysCallMeWithString(0, AnotherClassWithConstant::CONSTANT_NAME);
    }
}
