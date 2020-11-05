<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

class DisallowInsideOtherMethodNextDeadCode
{
    public function otherMethod()
    {
        $a = __DIR__ . '/static.txt';
        $a;
    }
}
