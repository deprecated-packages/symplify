<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\BoolishClassMethodPrefixRule\Source;

final class ClassWithEmptyReturn
{
    public function nothing(): void
    {
        return;
    }
}
