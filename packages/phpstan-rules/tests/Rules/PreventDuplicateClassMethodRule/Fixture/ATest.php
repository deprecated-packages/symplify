<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

class ATest
{
    public function someMethod()
    {
        (new SmartFinder())->run('.php');
    }
}
