<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoEntityManagerInControllerRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\NoEntityManagerInControllerRule\Source\SomeController;
use Doctrine\ORM\EntityManager;

final class UsingEntityManagerController extends SomeController
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
