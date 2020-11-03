<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoScalarAndArrayConstructorParameterRule\Fixture;

final class SomeWithConstructParameterNoType
{
    private $foo;

    public function __construct($foo)
    {
        $this->foo = $foo;
    }
}
