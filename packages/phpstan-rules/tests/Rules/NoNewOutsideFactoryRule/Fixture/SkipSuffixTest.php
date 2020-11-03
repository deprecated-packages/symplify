<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNewOutsideFactoryRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoNewOutsideFactoryRule\Source\SomeValueObject;

final class SkipSuffixTest
{
    public function run()
    {
        $someValueObject = new SomeValueObject();
        return $someValueObject;
    }
}
