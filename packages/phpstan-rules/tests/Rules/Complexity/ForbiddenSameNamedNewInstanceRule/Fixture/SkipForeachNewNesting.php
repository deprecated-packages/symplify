<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedNewInstanceRule\Fixture;

use PhpParser\Node\Expr\MethodCall;

final class SkipForeachNewNesting
{
    public function run(array $names, MethodCall $methodCall)
    {
        foreach ($names as $name) {
            $methodCall = new MethodCall($methodCall, $name);
        }
    }
}
