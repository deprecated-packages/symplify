<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoNewOutsideFactoryRule\Fixture;

final class SkipUnknownName
{
    public function run($name)
    {
        $someValueObject = new $name();
        return $someValueObject;
    }
}
