<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNullableParameterRule\FixtureDefaultConfiguration;

final class MethodWithNullableScalar
{
    public function run(?string $name): void
    {
    }
}
