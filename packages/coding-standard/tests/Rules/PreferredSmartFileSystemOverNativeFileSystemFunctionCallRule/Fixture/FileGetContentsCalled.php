<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\PreferredSmartFileSystemOverNativeFileSystemFunctionCallRule\Fixture;

final class PregMatchCalled
{
    public function run()
    {
        return file_get_contents('foo.txt');
    }
}
