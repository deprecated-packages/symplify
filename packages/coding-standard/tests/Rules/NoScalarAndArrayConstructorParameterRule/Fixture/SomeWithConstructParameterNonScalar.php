<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoScalarAndArrayConstructorParameterRule\Fixture;

use stdClass;

final class SomeWithConstructParameterNonScalar
{
    public function __construct(stdClass $stdClass)
    {
    }
}
