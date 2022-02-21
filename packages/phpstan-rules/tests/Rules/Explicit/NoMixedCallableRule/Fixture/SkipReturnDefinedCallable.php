<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedCallableRule\Fixture;

final class SkipReturnDefinedCallable
{
    /**
     * @param callable(): int $callable
     */
    public function run(callable $callable, $value)
    {
        return $callable($value);
    }
}
