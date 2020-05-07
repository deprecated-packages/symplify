<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\BoolishClassMethodPrefixRule\Source;

final class ClassWithBoolishMethods
{
    public function honesty()
    {
        return true;
    }

    public function thatWasGreat()
    {
        if (random_int(1, 3)) {
            return true;
        }

        return false;
    }
}
