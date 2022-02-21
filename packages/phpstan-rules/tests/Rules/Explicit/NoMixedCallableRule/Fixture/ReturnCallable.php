<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedCallableRule\Fixture;

final class ReturnCallable
{
    /**
     * @param callable(): int $callable
     */
    public function run(callable $callable, $value): callable
    {
        return $callable($value);
    }
}
