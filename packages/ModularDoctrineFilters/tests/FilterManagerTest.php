<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\FilterCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_Assert;
use Symplify\ModularDoctrineFilters\Contract\FilterManagerInterface;
use Symplify\ModularDoctrineFilters\FilterManager;
use Symplify\ModularDoctrineFilters\Tests\Filter\SomeFilter;

final class FilterManagerTest extends TestCase
{
    /**
     * @var FilterManagerInterface
     */
    private $filterManager;

    /**
     * @var FilterCollection
     */
    private $filterCollection;

    protected function setUp()
    {
        $this->filterManager = $this->createFilterManager();
    }

    public function testAddFilter()
    {
        $this->filterManager->addFilter('some', new SomeFilter);

        $this->assertCount(
            1,
            PHPUnit_Framework_Assert::getObjectAttribute($this->filterManager, 'filters')
        );
    }

    public function testEnableFilters()
    {
        $this->filterManager->addFilter('some', new SomeFilter);
        $this->filterManager->addFilter('some_other', new SomeFilter);

        $this->assertCount(0, $this->filterCollection->getEnabledFilters());

        $this->filterManager->enableFilters();

        $this->assertCount(2, $this->filterCollection->getEnabledFilters());
    }

    private function createFilterManager() : FilterManagerInterface
    {
        $entityManagerMock = $this->prophesize(EntityManagerInterface::class);
        $this->filterCollection = new FilterCollection($entityManagerMock->reveal());
        $entityManagerMock->getFilters()
            ->willReturn($this->filterCollection);

        return new FilterManager($entityManagerMock->reveal());
    }
}
