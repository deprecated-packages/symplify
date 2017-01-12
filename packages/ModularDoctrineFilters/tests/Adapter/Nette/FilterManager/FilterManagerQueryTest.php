<?php declare(strict_types=1);

namespace Symplify\DoctrineFilters\Tests\FilterManager;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symplify\DoctrineFilters\Contract\FilterManagerInterface;
use Symplify\DoctrineFilters\FilterManager;
use Symplify\DoctrineFilters\Tests\ContainerFactory;
use Symplify\DoctrineFilters\Tests\Entity\Product;

final class FilterManagerQueryTest extends TestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ObjectRepository
     */
    private $productRepository;

    /**
     * @var FilterManagerInterface
     */
    private $filterManager;

    protected function setUp()
    {
        $container = (new ContainerFactory)->create();
        $this->entityManager = $container->getByType(EntityManagerInterface::class);
        $this->filterManager = $container->getByType(FilterManager::class);
        $this->productRepository = $this->entityManager->getRepository(Product::class);

        $this->prepareDbData($container->getByType(Connection::class));
    }

    public function testFindOneBy()
    {
        $this->filterManager->enableFilters();

        $product = $this->productRepository->findOneBy(['id' => 1]);
        $this->assertInstanceOf(Product::class, $product);
        $this->assertTrue($product->isActive());

        $product2 = $this->productRepository->findOneBy(['id' => 2]);
        $this->assertNull($product2);

        // this should be NULL; this appears only in CLI
        $product2 = $this->productRepository->find(2);
        $this->assertInstanceOf(Product::class, $product2);
        $this->assertFalse($product2->isActive());
    }

    private function prepareDbData(Connection $connection)
    {
        $connection->query('CREATE TABLE product (id INTEGER NOT NULL, name string, is_active int NULL, PRIMARY KEY(id))');

        $this->entityManager->persist(new Product(true));
        $this->entityManager->persist(new Product(false));
        $this->entityManager->flush();
    }
}
