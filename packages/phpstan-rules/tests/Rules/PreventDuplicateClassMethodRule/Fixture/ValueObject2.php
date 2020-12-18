<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

class ValueObject2
{
    public function someMethod(string $x)
    {
        return $x;
    }
}
