<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoScalarAndArrayConstructorParameterRule\Fixture;

final class ParamBoolType
{
    /**
     * @var bool
     */
    private $bool;

    /**
     * @param bool $bool
     */
    public function __construct($bool)
    {
        $this->bool = $bool;
    }
}
