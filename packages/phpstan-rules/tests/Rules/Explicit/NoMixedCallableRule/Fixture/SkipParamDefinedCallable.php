<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedPropertyFetcherRule\Fixture;

final class SkipParamDefinedCallable
{
    /**
     * @param callable(array $callable): mixed $callable
     */
    public function run(callable $callable, $value)
    {
        return $callable($value);
    }
}
