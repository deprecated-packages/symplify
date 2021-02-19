<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireStringArgumentInMethodCallRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\RequireStringArgumentInMethodCallRule\Source\AlwaysCallMeWithString;

final class SkipWithVariable
{
    public function run(): void
    {
        $value = 'someType';

        new AlwaysCallMeWithString(0, $value);
    }
}
