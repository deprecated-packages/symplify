<?php declare(strict_types=1);

namespace Symplify\DoctrineExtensionsTree\Tests\Tree;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Gedmo\Tree\Entity\Repository\MaterializedPathRepository;
use Gedmo\Tree\TreeListener;
use Nette\DI\Container;
use PHPUnit\Framework\TestCase;
use Symplify\DoctrineExtensionsTree\Tests\ContainerFactory;
use Symplify\DoctrineExtensionsTree\Tests\DatabaseLoader;
use Symplify\DoctrineExtensionsTree\Tests\Project\Entities\Category;

final class TreeTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var ObjectRepository|MaterializedPathRepository
     */
    private $categoryRepository;

    protected function setUp(): void
    {
        $this->container = (new ContainerFactory)->create();

        /** @var EntityManager $entityManager */
        $entityManager = $this->container->getByType(EntityManager::class);
        $this->categoryRepository = $entityManager->getRepository(Category::class);

        /** @var DatabaseLoader $databaseLoader */
        $databaseLoader = $this->container->getByType(DatabaseLoader::class);
        $databaseLoader->prepareCategoryTableWithTwoItems();
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(
            TreeListener::class,
            $this->container->getByType(TreeListener::class)
        );
    }

    public function testParent(): void
    {
        /** @var Category $category */
        $category = $this->categoryRepository->find(2);
        $this->assertInstanceOf(Category::class, $category);
        $this->assertSame('Apple', $category->getName());

        $this->assertInstanceOf(Category::class, $category->getParent());
        $this->assertSame('Fruit', $category->getParent()->getName());
    }

    public function testPath(): void
    {
        /** @var Category $category */
        $category = $this->categoryRepository->find(1);
        $this->assertSame('Fruit-1|', $category->getPath());

        /** @var Category $category */
        $category = $this->categoryRepository->find(2);
        $this->assertSame('Fruit-1|Apple-2|', $category->getPath());
    }

    public function testTreeRepository(): void
    {
        $category = $this->categoryRepository->find(1);
        /** @var Category[] $children */
        $children = $this->categoryRepository->getChildren($category);
        $this->assertCount(1, $children);
        $this->assertSame('Apple', $children[0]->getName());
    }
}
