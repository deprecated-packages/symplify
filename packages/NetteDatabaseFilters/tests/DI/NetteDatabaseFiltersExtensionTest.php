<?php declare(strict_types=1);

namespace Zenify\NetteDatabaseFilters\Tests\DI;

use Nette\Database\Context;
use Nette\DI\Container;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Assert;
use Zenify\NetteDatabaseFilters\Contract\FilterManagerInterface;
use Zenify\NetteDatabaseFilters\Database\FiltersAwareContext;
use Zenify\NetteDatabaseFilters\Tests\ContainerFactory;

final class NetteDatabaseFiltersExtensionTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    protected function setUp()
    {
        $this->container = (new ContainerFactory)->create();
    }

    public function testContextWasReplaced()
    {
        $databaseContext = $this->container->getByType(Context::class);
        $this->assertInstanceOf(FiltersAwareContext::class, $databaseContext);
    }

    public function testFiltersAreCollected()
    {
        $filterManager = $this->container->getByType(FilterManagerInterface::class);
        $this->assertCount(2, Assert::getObjectAttribute($filterManager, 'filters'));
    }
}
