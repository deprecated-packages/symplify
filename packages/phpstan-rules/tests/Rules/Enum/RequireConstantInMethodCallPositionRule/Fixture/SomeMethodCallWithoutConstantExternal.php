<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\RequireConstantInMethodCallPositionRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Enum\RequireConstantInMethodCallPositionRule\Source\AlwaysCallMeWithConstantExternal;

final class SomeMethodCallWithoutConstantExternal
{
    public function run(): void
    {
        $alwaysCallMeWithConstant = new AlwaysCallMeWithConstantExternal();
        $alwaysCallMeWithConstant->call('some_type');
    }
}
