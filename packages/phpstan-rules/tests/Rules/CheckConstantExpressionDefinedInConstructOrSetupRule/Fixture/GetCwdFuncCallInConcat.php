<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

class GetCwdFuncCallInConcat
{
    public function otherMethod()
    {
        $value = getcwd() . getcwd();
    }
}
