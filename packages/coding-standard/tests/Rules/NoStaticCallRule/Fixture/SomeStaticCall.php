<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoStaticCallRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\NoStaticCallRule\Source\SomeMethods;

final class SomeStaticCall
{
    public function someMethod()
    {
        SomeMethods::stand();
    }
}
