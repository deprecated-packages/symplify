<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\PreferredSmartFileSystemOverNativeFileSystemFunctionCallRule\Fixture;

final class PregMatchCalled
{
    public function run()
    {
        return preg_match('pattern', 'value');
    }
}
