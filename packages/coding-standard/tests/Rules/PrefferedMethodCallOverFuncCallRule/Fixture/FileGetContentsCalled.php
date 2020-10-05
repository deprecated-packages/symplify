<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\PrefferedMethodCallOverFuncCallRule\Fixture;

final class PregMatchCalled
{
    public function run()
    {
        return file_get_contents('foo.txt');
    }
}
