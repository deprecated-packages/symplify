<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

class SkipConstruct
{
    public function __construct()
    {
        echo '__construct';
    }
}
