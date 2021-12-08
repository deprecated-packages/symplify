<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireStringArgumentInConstructorRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\RequireStringArgumentInConstructorRule\Source\AlwaysCallMeWithString;

final class SkipWithVariable
{
    public function run(): void
    {
        $value = 'someType';

        new AlwaysCallMeWithString(0, $value);
    }
}
