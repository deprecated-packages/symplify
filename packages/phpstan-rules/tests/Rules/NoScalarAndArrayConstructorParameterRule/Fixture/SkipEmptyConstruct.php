<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoScalarAndArrayConstructorParameterRule\Fixture;

final class SkipEmptyConstruct
{
    public function __construct()
    {
    }
}
