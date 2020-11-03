<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireConstantInMethodCallPositionRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\RequireConstantInMethodCallPositionRule\Source\AlwaysCallMeWithConstantExternal;
use Symplify\PHPStanRules\Tests\Rules\RequireConstantInMethodCallPositionRule\Source\AlwaysCallMeWithConstantLocal;

final class SkipWithVariable
{
    public function run($variable): void
    {
        $alwaysCallMeWithConstant = new AlwaysCallMeWithConstantLocal();
        $alwaysCallMeWithConstant->call($variable);

        $alwaysCallMeWithConstant = new AlwaysCallMeWithConstantExternal();
        $alwaysCallMeWithConstant->call($variable);
    }
}
