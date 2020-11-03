<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoProtectedElementInFinalClassRule\Fixture;

class SomeNotFinalClass
{
    protected $x;
    protected function run()
    {
    }
}
