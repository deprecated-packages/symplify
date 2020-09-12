<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoNewOutsideFactoryRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\NoNewOutsideFactoryRule\Source\AnotherClass;
use Symplify\CodingStandard\Tests\Rules\NoNewOutsideFactoryRule\Source\SomeValueObject;

final class SkipReturnedDifferentNode
{
    public function run(AnotherClass $anotherClass)
    {
        $someValueObject = new SomeValueObject();

        return $anotherClass;
    }
}
