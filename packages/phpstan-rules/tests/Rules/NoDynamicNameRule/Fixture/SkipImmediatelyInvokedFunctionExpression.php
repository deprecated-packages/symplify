<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicNameRule\Fixture;

final class SkipImmediatelyInvokedFunctionExpression
{
    public function load(string $key, string $variableKey)
    {
        return (function (string $key, string $variableKey) {
            return "hello";
        })($key, $variableKey);
    }
}
