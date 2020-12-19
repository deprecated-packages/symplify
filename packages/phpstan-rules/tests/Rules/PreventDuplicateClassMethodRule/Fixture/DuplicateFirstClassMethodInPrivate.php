<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

class DuplicateFirstClassMethodInPrivate
{
    private function someMethod()
    {
        echo 'statement';
        (new SmartFinder())->run('.php');
    }
}
