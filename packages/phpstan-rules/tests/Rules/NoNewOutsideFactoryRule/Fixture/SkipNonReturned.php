<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoNewOutsideFactoryRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\NoNewOutsideFactoryRule\Source\SomeValueObject;

final class SkipNonReturned
{
    public function run()
    {
        $someValueObject = new SomeValueObject();
    }
}
