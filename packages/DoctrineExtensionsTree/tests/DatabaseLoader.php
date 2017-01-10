<?php declare(strict_types=1);

namespace Zenify\DoctrineExtensionsTree\Tests;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Zenify\DoctrineExtensionsTree\Tests\Project\Entities\Category;

final class DatabaseLoader
{
    /**
     * @var bool
     */
    private $isDbSchemaPrepared = false;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(Connection $connection, EntityManager $entityManager)
    {
        $this->connection = $connection;
        $this->entityManager = $entityManager;
    }

    public function prepareCategoryTableWithTwoItems()
    {
        if (! $this->isDbSchemaPrepared) {
            /** @var Connection $connection */
            $this->connection->query('CREATE TABLE category (id INTEGER NOT NULL, parent_id int NULL,'
                . 'path string, name string, PRIMARY KEY(id))');

            $fruitCategory = new Category('Fruit');
            $appleCategory = new Category('Apple', $fruitCategory);

            $this->entityManager->persist($fruitCategory);
            $this->entityManager->persist($appleCategory);
            $this->entityManager->flush();
            $this->isDbSchemaPrepared = true;
        }
    }
}
