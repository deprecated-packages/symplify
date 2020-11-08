<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredStaticCallOverFuncCallRule\Fixture;

final class SkipSelfCall
{
    public function run()
    {
        return substr('...', '.');
    }
}
