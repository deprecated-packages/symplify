<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\AllowedExclusiveDependencyRule\Fixture;

use Doctrine\ORM\EntityManager;

final class SomeController
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}
