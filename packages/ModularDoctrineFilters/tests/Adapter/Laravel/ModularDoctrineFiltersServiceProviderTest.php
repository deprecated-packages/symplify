<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Tests\Adapter\Laravel;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Foundation\Application;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symplify\ModularDoctrineFilters\Adapter\Laravel\ModularDoctrineFiltersServiceProvider;
use Symplify\ModularDoctrineFilters\Contract\Filter\FilterInterface;
use Symplify\ModularDoctrineFilters\Contract\FilterManagerInterface;
use Symplify\ModularDoctrineFilters\FilterManager;
use Symplify\ModularDoctrineFilters\Tests\Source\Filter\SomeFilter;

final class ModularDoctrineFiltersServiceProviderTest extends TestCase
{
    /**
     * @var Application
     */
    private $application;

    protected function setUp(): void
    {
        $this->application = new Application;
        $this->setupDoctrineDependency($this->application);
        $this->application->register(new ModularDoctrineFiltersServiceProvider($this->application));
    }

    public function testRegister(): void
    {
        $filterManagerByInterface = $this->application->make(FilterManagerInterface::class);
        $filterManagerByClass = $this->application->make(FilterManager::class);

        $this->assertSame($filterManagerByClass, $filterManagerByInterface);

        $this->assertInstanceOf(FilterManager::class, $filterManagerByInterface);
        $this->assertInstanceOf(FilterManagerInterface::class, $filterManagerByInterface);
    }

    public function testBoot(): void
    {
        $this->registerFilterServices($this->application);

        $this->application->boot();

        /** @var FilterManagerInterface $filterManager */
        $filterManager = $this->application->make(FilterManagerInterface::class);

        $this->assertCount(
            2,
            Assert::getObjectAttribute($filterManager, 'filters')
        );
    }

    private function setupDoctrineDependency(Application $application): void
    {
        $application->singleton(EntityManager::class, function () {
            return $this->prophesize(EntityManagerInterface::class)
                ->reveal();
        });

        $application->alias(EntityManager::class, EntityManagerInterface::class);
    }

    private function registerFilterServices(Application $application): void
    {
        $application->bind(SomeFilter::class);

        $application->bind('filter_2', function (): FilterInterface {
            return $this->prophesize(FilterInterface::class)
                ->reveal();
        });

        // @todo: add more possible ways to register service
//        $application->bind('filter_3', function () {
//            return $this->prophesize(FilterInterface::class)
//                ->reveal();
//        });
    }
}
