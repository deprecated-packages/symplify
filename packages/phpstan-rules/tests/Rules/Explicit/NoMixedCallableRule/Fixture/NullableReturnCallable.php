<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedCallableRule\Fixture;

final class NullableReturnCallable
{
    /**
     * @param callable(): int $callable
     */
    public function run(callable $callable, $value): ?callable
    {
        if ($value) {
            return $callable($value);
        }

        return null;
    }
}
