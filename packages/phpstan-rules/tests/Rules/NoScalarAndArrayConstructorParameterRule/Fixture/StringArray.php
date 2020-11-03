<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoScalarAndArrayConstructorParameterRule\Fixture;

final class StringArray
{
    /**
     * @var string[]
     */
    private $array;

    /**
     * @param string[] $array
     */
    public function __construct(array $array)
    {
        $this->array = $array;
    }
}
