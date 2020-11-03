<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireMethodCallArgumentConstantRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\RequireMethodCallArgumentConstantRule\Source\AlwaysCallMeWithConstant;

final class SomeMethodCallWithoutConstant
{
    public function run(): void
    {
        $alwaysCallMeWithConstant = new AlwaysCallMeWithConstant();
        $alwaysCallMeWithConstant->call('some_type');
    }
}
