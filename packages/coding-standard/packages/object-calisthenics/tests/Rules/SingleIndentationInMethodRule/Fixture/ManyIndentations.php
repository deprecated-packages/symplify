<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\SingleIndentationInMethodRule\Fixture;

final class ManyIndentations
{
    public function someMethod()
    {
        if (true) {
            if (false) {
                return 'maybe';
            }
        }

        return 'sure';
    }
}
