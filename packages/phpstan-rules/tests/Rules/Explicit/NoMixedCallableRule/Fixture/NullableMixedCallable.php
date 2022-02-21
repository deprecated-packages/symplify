<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedCallableRule\Fixture;

final class NullableMixedCallable
{
    public function run(null|callable $callable, $value)
    {
        return $callable($value);
    }
}
