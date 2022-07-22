<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Source\PassMeAsType;

final class SkipExpectedClassType
{
    public function callMeWithClassType(PassMeAsType $passMeAsType)
    {
    }
}
