<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\TooLongFunctionLikeRule\Fixture;

final class SkipShortMethod
{
    public function someMethod()
    {
        $value = 100;
        $value = 100;
        $value = 100;
    }
}
