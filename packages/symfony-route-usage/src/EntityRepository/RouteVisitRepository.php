<?php

declare(strict_types=1);

namespace Symplify\SymfonyRouteUsage\EntityRepository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symplify\SymfonyRouteUsage\Database\TableInitiator;
use Symplify\SymfonyRouteUsage\Entity\RouteVisit;

/**
 * @see \Symplify\SymfonyRouteUsage\Tests\EntityRepository\RouteVisitRepositoryTest
 */
final class RouteVisitRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ObjectRepository
     */
    private $objectRepository;

    /**
     * @var TableInitiator
     */
    private $tableInitiator;

    public function __construct(EntityManagerInterface $entityManager, TableInitiator $tableInitiator)
    {
        $this->entityManager = $entityManager;
        $this->objectRepository = $entityManager->getRepository(RouteVisit::class);
        $this->tableInitiator = $tableInitiator;
    }

    public function save(RouteVisit $routeVisit): void
    {
        $this->tableInitiator->initializeTableForEntity(RouteVisit::class);

        $this->entityManager->persist($routeVisit);
        $this->entityManager->flush();
    }

    public function findByRouteHash(string $routeHash): ?RouteVisit
    {
        $this->tableInitiator->initializeTableForEntity(RouteVisit::class);

        return $this->objectRepository->findOneBy([
            'routeHash' => $routeHash,
        ]);
    }

    /**
     * @return RouteVisit[]
     */
    public function fetchAll(): array
    {
        $this->tableInitiator->initializeTableForEntity(RouteVisit::class);

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->from(RouteVisit::class, 'r');
        $queryBuilder->select('r');
        $queryBuilder->orderBy('r.visitCount', 'DESC');

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }
}
