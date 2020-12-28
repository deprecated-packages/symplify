<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNullableParameterRule\Fixture;

final class SkipAllowedType
{
    public function run(?int $value = null): void
    {
    }
}
