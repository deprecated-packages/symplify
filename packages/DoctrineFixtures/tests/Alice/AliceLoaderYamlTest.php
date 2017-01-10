<?php declare(strict_types=1);

namespace Zenify\DoctrineFixtures\Tests\Alice;

use Zenify\DoctrineFixtures\Contract\Alice\AliceLoaderInterface;
use Zenify\DoctrineFixtures\Tests\AbstractDatabaseTestCase;
use Zenify\DoctrineFixtures\Tests\Entity\Product;
use Zenify\DoctrineFixtures\Tests\Faker\Provider\ProductName;

final class AliceLoaderYamlTest extends AbstractDatabaseTestCase
{
    /**
     * @var AliceLoaderInterface
     */
    private $fixturesLoader;

    protected function setUp()
    {
        parent::setUp();
        $this->fixturesLoader = $this->container->getByType(AliceLoaderInterface::class);
    }

    public function testLoadFixture()
    {
        $file = __DIR__ . '/AliceLoaderSource/products.yaml';

        /** @var Product[] $products */
        $products = $this->fixturesLoader->load($file);

        $this->assertCount(20, $products);

        foreach ($products as $product) {
            $this->assertInstanceOf(Product::class, $product);
            $this->assertInternalType('string', $product->getName());
            $this->assertContains($product->getName(), ProductName::$randomNames);
        }
    }
}
