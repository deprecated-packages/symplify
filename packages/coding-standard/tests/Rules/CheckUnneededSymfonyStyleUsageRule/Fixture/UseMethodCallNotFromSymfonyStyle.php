<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckUnneededSymfonyStyleUsageRule\Fixture;

use DateTime;

class UseMethodCallNotFromSymfonyStyle
{
    private $dateTime;

    public function __construct(DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public function run()
    {
        $this->dateTime->format('c');
    }
}
