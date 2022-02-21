<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedCallableRule\Fixture;

final class UnionMixedCallable
{
    public function run(string|callable $callable, $value)
    {
        return $callable($value);
    }
}
