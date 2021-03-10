<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoModifyAndReturnSelfObjectRule\Fixture;

use DateTime;

final class SkipNotReturnObject
{
    public function run(DateTime $dateTime)
    {
        $dateTime->format('Y-m-d');

        return 0;
    }
}
