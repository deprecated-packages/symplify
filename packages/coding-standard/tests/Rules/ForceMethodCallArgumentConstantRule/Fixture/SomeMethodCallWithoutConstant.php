<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForceMethodCallArgumentConstantRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\ForceMethodCallArgumentConstantRule\Source\AlwaysCallMeWithConstant;

final class SomeMethodCallWithoutConstant
{
    public function run(): void
    {
        $alwaysCallMeWithConstant = new AlwaysCallMeWithConstant();
        $alwaysCallMeWithConstant->call('some_type');
    }
}
