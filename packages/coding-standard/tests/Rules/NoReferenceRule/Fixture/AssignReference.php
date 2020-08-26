<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoReferenceRule\Fixture;

use PhpParser\Node\Expr\MethodCall;

class AssignReference
{
    public function run(MethodCall $node)
    {
        $arg = $node->args[1];
        $some = &$arg->value;
    }
}
