<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireConstantInMethodCallPositionRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\RequireConstantInMethodCallPositionRule\Source\AlwaysCallMeWithConstantLocal;

final class SomeMethodCallWithoutConstantLocal
{
    public function run(): void
    {
        $alwaysCallMeWithConstant = new AlwaysCallMeWithConstantLocal();
        $alwaysCallMeWithConstant->call('some_type');
    }
}
