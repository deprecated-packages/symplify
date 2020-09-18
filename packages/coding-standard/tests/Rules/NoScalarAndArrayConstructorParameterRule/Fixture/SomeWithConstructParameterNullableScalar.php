<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoScalarAndArrayConstructorParameterRule\Fixture;

final class SomeWithConstructParameterNullableScalar
{
    public function __construct(?string $string)
    {
    }
}
