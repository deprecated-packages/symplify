<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoScalarAndArrayConstructorParameterRule\Fixture;

final class SomeWithConstructParameterNullableNonScalar
{
    public function __construct(?stdClass $stdClass)
    {
    }
}
