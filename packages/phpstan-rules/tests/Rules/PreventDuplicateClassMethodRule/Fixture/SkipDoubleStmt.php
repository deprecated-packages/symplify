<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

class SkipDoubleStmt
{
    public function singleStmt()
    {
        $value = 1000;
        return 'single';
    }

    public function anotherOne()
    {
        $val = 1000;
        return 'single';
    }
}
