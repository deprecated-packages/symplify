<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Fixture;

use DateTime;

class SkipCloneCall
{
    public function run(DateTime $dateTime)
    {
        (clone $dateTime)->format('date');
    }
}
