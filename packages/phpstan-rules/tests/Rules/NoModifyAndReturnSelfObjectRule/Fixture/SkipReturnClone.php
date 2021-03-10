<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoModifyAndReturnSelfObjectRule\Fixture;

use DateTime;

final class SkipReturnClone
{
    public function run(DateTime $dateTime)
    {
        $new = clone $dateTime;
        $new->format('Y-m-d');

        return $new;
    }
}
