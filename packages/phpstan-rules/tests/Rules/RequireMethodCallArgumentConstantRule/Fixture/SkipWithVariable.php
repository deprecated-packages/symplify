<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireMethodCallArgumentConstantRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\RequireMethodCallArgumentConstantRule\Source\AlwaysCallMeWithConstant;

final class SkipWithVariable
{
    public function run($variable): void
    {
        $alwaysCallMeWithConstant = new AlwaysCallMeWithConstant();
        $alwaysCallMeWithConstant->call($variable);
    }
}
