<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoScalarAndArrayConstructorParameterRule\Fixture;

use stdClass;

final class SomeWithConstructParameterNullableNonScalar
{
    /**
     * @var stdClass|null
     */
    private $stdClass;

    public function __construct(?stdClass $stdClass)
    {
        $this->stdClass = $stdClass;
    }
}
