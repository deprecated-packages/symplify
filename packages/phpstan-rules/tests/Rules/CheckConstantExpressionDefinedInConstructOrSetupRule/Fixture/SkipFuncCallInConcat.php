<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

class SkipFuncCallInConcat
{
    public function otherMethod($value)
    {
        $value = str_repeat('a', 1) . str_repeat('b', 2);
    }
}
