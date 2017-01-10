<?php declare(strict_types=1);

namespace Zenify\DoctrineFixtures\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Nette\DI\Container;
use PHPUnit\Framework\TestCase;

abstract class AbstractDatabaseTestCase extends TestCase
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;


    protected function setUp()
    {
        $this->container = $container = (new ContainerFactory)->create();

        $this->entityManager = $this->container->getByType(EntityManagerInterface::class);

        /** @var DatabaseLoader $databaseLoader */
        $databaseLoader = $this->container->getByType(DatabaseLoader::class);
        $databaseLoader->prepareProductAndUserTable();
    }
}
