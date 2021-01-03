<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoScalarAndArrayConstructorParameterRule\Fixture\ValueObject\Deep;

final class SkipSomeConstruct
{
    public function __construct(string $string)
    {
    }
}
