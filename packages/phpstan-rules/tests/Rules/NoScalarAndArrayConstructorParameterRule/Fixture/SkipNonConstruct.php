<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoScalarAndArrayConstructorParameterRule\Fixture;

final class SkipNonConstruct
{
    public function run(string $name)
    {
        return $name;
    }
}
