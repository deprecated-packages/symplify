<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\RequireStringArgumentInMethodCallRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\RequireStringArgumentInMethodCallRule\Source\AlwaysCallMeWithString;

final class WithString
{
    public function run(): void
    {
        $alwaysCallMeWithString = new AlwaysCallMeWithString();
        $alwaysCallMeWithString->callMe(0, 'type');
    }
}
