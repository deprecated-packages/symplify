<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoDebugFuncCallRule\Fixture;

final class DebugFuncCall
{
    public function run()
    {
        dump('asdf');
    }
}
