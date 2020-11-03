<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckTraitMethodOnlyDelegateOtherClassRule\Fixture;

use DateTime;

trait CalllThisType
{
    public function run()
    {
        $this->isName('test');
    }
}
