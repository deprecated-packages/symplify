<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckTraitMethodOnlyDelegateOtherClassRule\Fixture;

use DateTime;

trait HasInstanceofCheck
{
    public function run()
    {
        if ($this->d instanceof DateTime) {
            $this->d->format('Y-m-d');
        }
    }
}
