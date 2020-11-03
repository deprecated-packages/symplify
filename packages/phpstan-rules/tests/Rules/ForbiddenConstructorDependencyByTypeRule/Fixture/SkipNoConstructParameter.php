<?php

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenConstructorDependencyByTypeRule\Fixture;

class SkipNoConstructParameter
{
    public function __construct()
    {
    }
}
