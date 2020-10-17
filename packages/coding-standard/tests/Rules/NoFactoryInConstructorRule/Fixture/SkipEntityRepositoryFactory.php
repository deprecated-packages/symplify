<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoFactoryInConstructorRule\Fixture;

use Doctrine\ORM\EntityManagerInterface;

final class SkipEntityRepositoryFactory
{
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository('SomeEntity');
    }
}
