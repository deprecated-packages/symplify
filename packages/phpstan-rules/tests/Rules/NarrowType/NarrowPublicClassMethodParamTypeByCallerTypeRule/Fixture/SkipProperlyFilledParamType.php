<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Fixture;

final class SkipProperlyFilledParamType
{
    public function callMeTwice(int $number)
    {
    }
}
