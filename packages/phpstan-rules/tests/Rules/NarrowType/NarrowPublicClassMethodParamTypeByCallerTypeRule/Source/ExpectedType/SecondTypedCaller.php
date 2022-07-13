<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Source\ExpectedType;

use Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Fixture\SkipProperlyFilledParamType;

final class SecondTypedCaller
{
    public function goForIt(SkipProperlyFilledParamType $skipProperlyFilledParamType)
    {
        $skipProperlyFilledParamType->callMeTwice(100);
    }
}
