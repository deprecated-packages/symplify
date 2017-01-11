<?php

declare(strict_types=1);

namespace Zenify\DoctrineFilters\Tests\FilterManager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\FilterCollection;
use PHPUnit\Framework\TestCase;
use Zenify\DoctrineFilters\Contract\FilterInterface;
use Zenify\DoctrineFilters\Contract\FilterManagerInterface;
use Zenify\DoctrineFilters\Tests\ContainerFactory;

final class FilterManagerTest extends TestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FilterManagerInterface
     */
    private $filterManager;

    protected function setUp()
    {
        $container = (new ContainerFactory)->create();
        $this->entityManager = $container->getByType(EntityManagerInterface::class);
        $this->filterManager = $container->getByType(FilterManagerInterface::class);
    }

    public function testEnabledFilters()
    {
        $filterCollection = $this->entityManager->getFilters();
        $this->assertInstanceOf(FilterCollection::class, $filterCollection);
        $this->assertCount(0, $filterCollection->getEnabledFilters());

        $this->filterManager->enableFilters();

        $this->assertCount(2, $filterCollection->getEnabledFilters());
        foreach ($filterCollection->getEnabledFilters() as $filter) {
            $this->assertInstanceOf(FilterInterface::class, $filter);
        }
    }
}
