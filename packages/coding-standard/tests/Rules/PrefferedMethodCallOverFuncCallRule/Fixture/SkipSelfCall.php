<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\PrefferedMethodCallOverFuncCallRule\Fixture;

final class SkipSelfCall
{
    public function run()
    {
        return copy('a.txt', 'b.txt');
    }
}
