<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoPostIncPostDecRule\Fixture;

final class WithPostInc
{
    public function run($value): void
    {
        $value++;
    }
}
