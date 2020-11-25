<?php

namespace Symplify\PHPStanRules\Tests\Rules\ExclusiveDependencyRule\Fixture;

use Doctrine\ORM\EntityManager;

class SomeController
{
    public function __construct(EntityManager $em)
    {

    }
}
