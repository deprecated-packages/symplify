<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenFuncCallRule\Fixture;

final class DebugFuncCall
{
    public function run()
    {
        dump('asdf');
    }
}
