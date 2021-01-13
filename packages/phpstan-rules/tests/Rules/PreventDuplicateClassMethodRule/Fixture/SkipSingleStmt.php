<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

class SkipSingleStmt
{
    public function singleStmt()
    {
        return 'single';
    }
}