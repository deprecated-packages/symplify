<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoStaticCallRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\NoStaticCallRule\Source\SomeStaticMethods;

final class SomeStaticCall
{
    public function someMethod()
    {
        SomeStaticMethods::stand();
    }
}
