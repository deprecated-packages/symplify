<?php

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenDependencyByTypeRule\Fixture;

class SkipNoConstructParameter
{
    public function __construct()
    {
    }
}
