<?php

namespace Symplify\PHPStanRules\Tests\Rules\ExclusiveDependencyRule\Fixture;

use Doctrine\ORM\EntityManager;

class SomeRepository
{
    public function __construct(EntityManager $entityManager)
    {
    }
}
