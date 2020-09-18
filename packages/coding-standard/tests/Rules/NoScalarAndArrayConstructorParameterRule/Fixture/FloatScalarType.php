<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoScalarAndArrayConstructorParameterRule\Fixture;

final class FloatScalarType
{
    /**
     * @var float
     */
    private $bool;

    /**
     * @param float $bool
     */
    public function __construct($bool)
    {
        $this->bool = $bool;
    }
}
