<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedPropertyFetcherRule\Fixture;

final class MixedCallable
{
    public function run(callable $callable, $value)
    {
        return $callable($value);
    }
}
