<?php


namespace Symplify\PHPStanRules\Tests\Rules\NoGetRepositoryOutsideConstructorRule\Fixture;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symplify\PHPStanRules\Tests\Rules\NoGetRepositoryOutsideConstructorRule\Source\TestRepository;

final class SkipTestCase
{
    /**
     * @var EntityRepository $testRepository
     */
    private $testRepository;

    protected function setUp(EntityManagerInterface $entityManager)
    {
        $this->testRepository = $entityManager->getRepository(TestRepository::class);
    }
}
