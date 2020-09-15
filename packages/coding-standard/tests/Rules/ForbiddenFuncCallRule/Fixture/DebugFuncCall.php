<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenFuncCallRule\Fixture;

final class DebugFuncCall
{
    public function run()
    {
        dump('asdf');
    }
}
