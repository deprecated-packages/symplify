<?php

namespace Symplify\DefaultAutowire\Tests\Resources\Repository;

use Doctrine\ORM\EntityManagerInterface;

final class SomeRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
}
