<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

class SecondClassDuplicateFirstClassMethod
{
    public function __construct()
    {
        echo '__construct';
        echo 'statement';
    }

    public function someMethod()
    {
        echo 'statement';
        (new SmartFinder())->run('.php');
    }
}
