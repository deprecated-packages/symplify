<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Source\ExpectedClassType;

use Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Fixture\SkipExpectedClassType;
use Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Source\PassMeAsType;

final class FirstClassTypedCaller
{
    public function goForIt(SkipExpectedClassType $skipExpectedClassType)
    {
        $knownType = new PassMeAsType();
        $skipExpectedClassType->callMeWithClassType($knownType);
    }
}
