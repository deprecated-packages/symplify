<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoProtectedElementInFinalClassRule\Fixture;

class SkipNotFinalClass
{
    protected $x;
    protected function run()
    {
    }
}
