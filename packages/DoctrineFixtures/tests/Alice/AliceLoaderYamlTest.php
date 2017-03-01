<?php declare(strict_types=1);

namespace Symplify\DoctrineFixtures\Tests\Alice;

use Symplify\DoctrineFixtures\Contract\Alice\AliceLoaderInterface;
use Symplify\DoctrineFixtures\Tests\AbstractDatabaseTestCase;
use Symplify\DoctrineFixtures\Tests\Entity\Product;
use Symplify\DoctrineFixtures\Tests\Faker\Provider\ProductName;

final class AliceLoaderYamlTest extends AbstractDatabaseTestCase
{
    /**
     * @var AliceLoaderInterface
     */
    private $fixturesLoader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixturesLoader = $this->container->getByType(AliceLoaderInterface::class);
    }

    public function testLoadFixture(): void
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
