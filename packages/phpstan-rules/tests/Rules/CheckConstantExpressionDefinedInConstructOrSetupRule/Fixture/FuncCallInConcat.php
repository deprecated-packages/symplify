<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

class FuncCallInConcat
{
    public function otherMethod()
    {
        $value = str_repeat('a', 1) . str_repeat('b', 2);
    }
}
