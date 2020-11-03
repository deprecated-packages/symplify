<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoScalarAndArrayConstructorParameterRule\Fixture;

final class IntScalarType
{
    /**
     * @var int
     */
    private $int;

    public function __construct(int $int)
    {
        $this->int = $int;
    }
}
