<?php

namespace Symplify\PHPStanRules\Tests\Rules\NoGetRepositoryOutsideConstructorRule\Fixture;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symplify\PHPStanRules\Tests\Rules\NoGetRepositoryOutsideConstructorRule\Source\TestRepository;

final class OneTestRepository
{
    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function find()
    {
        return $this->entityManager->getRepository(TestRepository::class)->findAll();
    }

}
