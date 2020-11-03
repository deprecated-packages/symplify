<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\RequireStringArgumentInMethodCallRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\RequireStringArgumentInMethodCallRule\Source\AlwaysCallMeWithString;

final class WithVariable
{
    public function run(): void
    {
        $value = 'someType';

        $alwaysCallMeWithString = new AlwaysCallMeWithString();
        $alwaysCallMeWithString->callMe(0, $value);
    }
}
