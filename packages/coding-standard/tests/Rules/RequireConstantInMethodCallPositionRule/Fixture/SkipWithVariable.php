<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\RequireConstantInMethodCallPositionRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\RequireConstantInMethodCallPositionRule\Source\AlwaysCallMeWithConstant;

final class SkipWithVariable
{
    public function run($variable): void
    {
        $alwaysCallMeWithConstant = new AlwaysCallMeWithConstant();
        $alwaysCallMeWithConstant->call($variable);
    }
}
