<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireConstantInMethodCallPositionRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\RequireConstantInMethodCallPositionRule\Source\AlwaysCallMeWithConstantExternal;
use Symplify\PHPStanRules\Tests\Rules\RequireConstantInMethodCallPositionRule\Source\SomeConstantList;

final class SkipWithConstantExternal
{
    public function run(): void
    {
        $alwaysCallMeWithConstant = new AlwaysCallMeWithConstantExternal();
        $alwaysCallMeWithConstant->call(SomeConstantList::TYPE);
    }
}
