<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Fixture;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;

final class SkipEqualUnionType
{
    public function run(MethodCall|StaticCall $obj)
    {
    }
}
