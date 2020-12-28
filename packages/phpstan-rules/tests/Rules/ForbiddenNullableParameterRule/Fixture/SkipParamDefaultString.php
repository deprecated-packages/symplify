<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNullableParameterRule\Fixture;

final class SkipParamDefaultString
{
    public function run($defaultValue = ''): void
    {
    }
}
