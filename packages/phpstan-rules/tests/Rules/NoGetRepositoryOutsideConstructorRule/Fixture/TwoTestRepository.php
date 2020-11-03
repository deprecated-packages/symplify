<?php


namespace Symplify\PHPStanRules\Tests\Rules\NoGetRepositoryOutsideConstructorRule\Fixture;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symplify\PHPStanRules\Tests\Rules\NoGetRepositoryOutsideConstructorRule\Source\TestRepository;

final class TwoTestRepository
{
    /**
     * @var EntityRepository $testRepository
     */
    private $testRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->testRepository = $entityManager->getRepository(TestRepository::class);
    }
}
