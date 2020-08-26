<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\RequireStringArgumentInMethodCallRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\RequireStringArgumentInMethodCallRule\Source\AlwaysCallMeWithString;
use Symplify\CodingStandard\Tests\Rules\RequireStringArgumentInMethodCallRule\Source\AnotherClassWithConstant;

final class WithConstant
{
    public function run(): void
    {
        $alwaysCallMeWithString = new AlwaysCallMeWithString();
        $alwaysCallMeWithString->callMe(0, AnotherClassWithConstant::CONSTANT_NAME);
    }
}
