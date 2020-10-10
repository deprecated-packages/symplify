<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\RequireConstantInMethodCallPositionRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\RequireConstantInMethodCallPositionRule\Source\AlwaysCallMeWithConstantExternal;

final class VO
{
    public const TYPE = 'correct';
}

final class WithConstantExternal
{
    public function run(): void
    {
        $alwaysCallMeWithConstant = new AlwaysCallMeWithConstantExternal();
        $alwaysCallMeWithConstant->call(VO::TYPE);
    }
}
