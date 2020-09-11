<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\BoolishClassMethodPrefixRule\Fixture;

final class ClassWithEmptyReturn
{
    public function nothing(): void
    {
        return;
    }
}
