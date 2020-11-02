<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoStaticCallRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\NoStaticCallRule\Source\AllowedMethods;

final class SkipAllowedStaticCall
{
    public function someMethod()
    {
        AllowedMethods::allowed();
    }
}
