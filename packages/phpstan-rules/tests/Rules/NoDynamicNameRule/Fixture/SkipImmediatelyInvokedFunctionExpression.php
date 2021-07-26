<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicNameRule\Fixture;

final class SkipImmediatelyInvokedFunctionExpression
{
    public function run(string $value)
    {
        return (function () use ($value) {
           return $value;
        })();
    }
}
