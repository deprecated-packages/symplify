<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Fixture;

use Nette\Utils\DateTime;

class SkipStaticCallDateTime
{
    public function run()
    {
        $starts_date = DateTime::from('now')->getOffset();

        $starts_date = DateTime::from()->getOffset();
    }
}
