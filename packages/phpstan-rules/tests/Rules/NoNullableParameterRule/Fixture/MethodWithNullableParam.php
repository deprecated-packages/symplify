<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNullableParameterRule\Fixture;

final class MethodWithNullableParam
{
    public function run(?string $value = null): void
    {
    }
}
