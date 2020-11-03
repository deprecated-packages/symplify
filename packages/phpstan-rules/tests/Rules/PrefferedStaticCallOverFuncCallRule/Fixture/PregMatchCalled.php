<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PrefferedStaticCallOverFuncCallRule\Fixture;

final class PregMatchCalled
{
    public function run()
    {
        return preg_match('pattern', 'value');
    }
}
