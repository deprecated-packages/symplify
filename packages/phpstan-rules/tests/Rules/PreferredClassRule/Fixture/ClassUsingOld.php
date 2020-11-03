<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\PreferredClassRule\Fixture;

use DateTime;

final class ClassUsingOld
{
    public function run()
    {
        $dateTime = new DateTime();
        return $dateTime;
    }
}
