<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoScalarConstructorParameterRule\Fixture;

final class SomeWithConstructParameterNotype
{
    public function __construct($foo)
    {
    }
}
