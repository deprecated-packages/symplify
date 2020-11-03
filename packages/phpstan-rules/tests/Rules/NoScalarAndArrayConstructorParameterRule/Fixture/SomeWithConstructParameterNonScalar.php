<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoScalarAndArrayConstructorParameterRule\Fixture;

use stdClass;

final class SomeWithConstructParameterNonScalar
{
    /**
     * @var stdClass
     */
    private $stdClass;

    public function __construct(stdClass $stdClass)
    {
        $this->stdClass = $stdClass;
    }
}
