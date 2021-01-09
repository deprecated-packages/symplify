<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckTraitMethodOnlyDelegateOtherClassRule\Fixture;

use DateTime;

trait CallThisType
{
    public function run()
    {
        $this->isName('test');
    }
}
