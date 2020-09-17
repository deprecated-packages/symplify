<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoStaticPropertyRule\Fixture;

class SomeNotFinalClass
{
    protected $x;
    protected function run()
    {
    }
}
