<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoScalarAndArrayConstructorParameterRule\Fixture;

final class BoolScalarType
{
    /**
     * @var bool
     */
    private $bool;

    public function __construct(bool $bool)
    {
        $this->bool = $bool;
    }
}
