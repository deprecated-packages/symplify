<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ObjectCalisthenics\NoElseRule\Source;

final class SomeElse
{
    public function run()
    {
        if (true) {
            return 5;
        } else {
            return 1;
        }

    }
}
