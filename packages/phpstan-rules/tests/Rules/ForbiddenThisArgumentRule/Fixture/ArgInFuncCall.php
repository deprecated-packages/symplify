<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenThisArgumentRule\Fixture;

final class ArgInFuncCall
{
    public function run()
    {
        return strlen($this);
    }
}
