<?php declare(strict_types=1);

namespace Zenify\DoctrineFixtures\Tests\Alice;

use Doctrine\Common\Persistence\ObjectRepository;
use Zenify\DoctrineFixtures\Contract\Alice\AliceLoaderInterface;
use Zenify\DoctrineFixtures\Tests\AbstractDatabaseTestCase;
use Zenify\DoctrineFixtures\Tests\Entity\Product;
use Zenify\DoctrineFixtures\Tests\Entity\User;
use Zenify\DoctrineFixtures\Tests\Faker\Provider\ProductName;

final class AliceLoaderTest extends AbstractDatabaseTestCase
{
    /**
     * @var AliceLoaderInterface
     */
    private $fixturesLoader;

    /**
     * @var ObjectRepository
     */
    private $productRepository;

    /**
     * @var ObjectRepository
     */
    private $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixturesLoader = $this->container->getByType(AliceLoaderInterface::class);
        $this->productRepository = $this->entityManager->getRepository(Product::class);
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    public function testLoadFixture(): void
    {
        $file = __DIR__ . '/AliceLoaderSource/products.neon';
        $this->fixturesLoader->load($file);

        $products = $this->productRepository->findAll();
        $this->assertCount(100, $products);

        /** @var Product $product */
        foreach ($products as $product) {
            $this->assertInstanceOf(Product::class, $product);
            $this->assertInternalType('string', $product->getName());
            $this->assertContains($product->getName(), ProductName::$randomNames);
        }
    }

    public function testLoadFolder(): void
    {
        $this->fixturesLoader->load(__DIR__ . '/AliceLoaderSource');

        $products = $this->productRepository->findAll();
        $this->assertCount(120, $products);

        $users = $this->userRepository->findAll();
        $this->assertCount(15, $users);

        /** @var User $user */
        foreach ($users as $user) {
            $this->assertInstanceOf(User::class, $user);
            $this->assertContains('@', $user->getEmail());
        }
    }

    public function testLoadFixtureWithIncludesFixturesAreLoadedInTopDownOrder(): void
    {
        $file = __DIR__ . '/AliceLoaderSource/includes.neon';
        $this->fixturesLoader->load($file);

        /** @var User[] $users */
        $users = $this->userRepository->findAll();

        $this->assertCount(2, $users);

        $this->assertInstanceOf(User::class, $users[0]);
        $this->assertSame('user1@email.com', $users[0]->getEmail());
        $this->assertInstanceOf(User::class, $users[1]);
        $this->assertSame('user2@email.com', $users[1]->getEmail());
    }

    /**
     * @expectedException \Zenify\DoctrineFixtures\Exception\MissingSourceException
     */
    public function testLoadFromNonExistingSource(): void
    {
        $this->fixturesLoader->load(__DIR__ . '/not-in-here');
    }
}
