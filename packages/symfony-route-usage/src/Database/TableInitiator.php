<?php

declare(strict_types=1);

namespace Symplify\SymfonyRouteUsage\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

final class TableInitiator
{
    /**
     * @var AbstractSchemaManager
     */
    private $schemaManager;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SchemaTool
     */
    private $schemaTool;

    public function __construct(Connection $connection, EntityManagerInterface $entityManager, SchemaTool $schemaTool)
    {
        $this->schemaManager = $connection->getSchemaManager();
        $this->entityManager = $entityManager;
        $this->schemaTool = $schemaTool;
    }

    public function initializeTableForEntity(string $entityClass): void
    {
        $routeVisitClassMetadata = $this->entityManager->getClassMetadata($entityClass);

        if ($this->schemaManager->tablesExist([$routeVisitClassMetadata->getTableName()])) {
            return;
        }

        $this->schemaTool->createSchema([$routeVisitClassMetadata]);
    }
}
