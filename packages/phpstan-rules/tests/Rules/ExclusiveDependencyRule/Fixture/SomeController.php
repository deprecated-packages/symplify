<?php

namespace Symplify\PHPStanRules\Tests\Rules\ExclusiveDependencyRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\ExclusiveDependencyRule\Source\CustomEntityManager;

class SomeController
{
    public function __construct(CustomEntityManager $em)
    {
    }
}
