<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireStringArgumentInMethodCallRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\RequireStringArgumentInMethodCallRule\Source\AlwaysCallMeWithString;

final class WithVariable
{
    public function run(): void
    {
        $value = 'someType';

        $alwaysCallMeWithString = new AlwaysCallMeWithString();
        $alwaysCallMeWithString->callMe(0, $value);
    }
}
