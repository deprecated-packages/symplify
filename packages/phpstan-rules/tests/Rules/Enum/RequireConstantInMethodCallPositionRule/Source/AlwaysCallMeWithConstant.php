<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\RequireConstantInMethodCallPositionRule\Source;

final class AlwaysCallMeWithConstant
{
    public function call(string $type)
    {
    }
}
