<?php declare(strict_types=1);

namespace Zenify\DoctrineFixtures\Tests;

use Doctrine\DBAL\Connection;

final class DatabaseLoader
{

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var bool
     */
    private $isDbSchemaPrepared;


    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }


    public function prepareProductAndUserTable()
    {
        if (! $this->isDbSchemaPrepared) {
            $this->connection->query('CREATE TABLE product (id INTEGER NOT NULL, name string, PRIMARY KEY(id))');
            $this->connection->query('CREATE TABLE user (id INTEGER NOT NULL, email string, PRIMARY KEY(id))');
            $this->isDbSchemaPrepared = true;
        }
    }
}
