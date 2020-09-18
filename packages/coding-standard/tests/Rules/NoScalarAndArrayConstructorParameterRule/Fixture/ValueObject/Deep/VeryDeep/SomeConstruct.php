<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoScalarAndArrayConstructorParameterRule\Fixture\ValueObject\Deep\VeryDeep;

final class SomeConstruct
{
    public function __construct(string $string)
    {
    }
}
