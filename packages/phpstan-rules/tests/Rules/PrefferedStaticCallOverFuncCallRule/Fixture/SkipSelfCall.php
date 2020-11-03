<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PrefferedStaticCallOverFuncCallRule\Fixture;

final class SkipSelfCall
{
    public function run()
    {
        return substr('...', '.');
    }
}
