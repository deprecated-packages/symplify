<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Tests\Filter;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\FilterCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_Assert;
use Symplify\ModularDoctrineFilters\Contract\Filter\FilterManagerInterface;
use Symplify\ModularDoctrineFilters\Filter\FilterManager;

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
        $entityManagerMock = $this->prophesize(EntityManagerInterface::class);
        $this->filterCollection = new FilterCollection($entityManagerMock->reveal());
        $entityManagerMock->getFilters()->willReturn($this->filterCollection);

        $this->filterManager = new FilterManager($entityManagerMock->reveal());
    }

    public function testAddFilter()
    {
        $this->filterManager->addFilter('some', new SomeFilter());

        $this->assertCount(
            1,
            PHPUnit_Framework_Assert::getObjectAttribute($this->filterManager, 'filters')
        );
    }

    public function testEnableFilters()
    {
        $this->filterManager->addFilter('some', new SomeFilter());
        $this->filterManager->addFilter('some_other', new SomeFilter());

        $this->assertCount(0, $this->filterCollection->getEnabledFilters());

        $this->filterManager->enableFilters();

        $this->assertCount(2, $this->filterCollection->getEnabledFilters());
    }
}
