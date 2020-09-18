<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoScalarConstructorParameterRule\Fixture;

final class SomeWithConstructParameterScalar
{
    public function __construct(string $string)
    {
    }
}

final class SomeWithConstructParameterScalar2
{
    public function __construct(int $int)
    {
    }
}

final class SomeWithConstructParameterScalar3
{
    public function __construct(float $bool)
    {
    }
}

final class SomeWithConstructParameterScalar4
{
    public function __construct(bool $bool)
    {
    }
}
