<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\PreferredClassRule\Fixture;

use DateTime;

final class StaticCall
{
    public function run()
    {
        $lastErrors = DateTime::getLastErrors();
    }
}
