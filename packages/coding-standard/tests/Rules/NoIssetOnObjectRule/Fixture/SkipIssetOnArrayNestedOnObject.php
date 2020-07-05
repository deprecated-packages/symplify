<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoIssetOnObjectRule\Fixture;

use PhpParser\Node\Expr\MethodCall;

final class SkipIssetOnArrayNestedOnObject
{
    public function run(MethodCall $methodCall)
    {
        if (isset($methodCall->args[9])) {
            return $methodCall->args[9];
        }
    }
}
