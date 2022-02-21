<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedCallableRule\Fixture;

final class DocOnlyNullableReturnCallable
{
    /**
     * @param callable(): int $callable
     * @return callable|null
     */
    public function run(callable $callable, $value)
    {
        return $callable($value);
    }
}
