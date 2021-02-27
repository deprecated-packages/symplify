<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoInlineStringRegexRule\Fixture;

use DateTime;

final class ModifyAndReturnSelfObject
{
    public function run(DateTime $dateTime)
    {
        $dateTime->format('Y-m-d');
        return $dateTime;
    }
}
