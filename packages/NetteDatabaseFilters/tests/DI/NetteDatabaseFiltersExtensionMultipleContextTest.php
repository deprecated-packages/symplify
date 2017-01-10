<?php declare(strict_types=1);

namespace Zenify\NetteDatabaseFilters\Tests\DI;

use Nette\Database\Context;
use Nette\DI\Container;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_Assert;
use Zenify\NetteDatabaseFilters\Contract\FilterManagerInterface;
use Zenify\NetteDatabaseFilters\Database\FiltersAwareContext;
use Zenify\NetteDatabaseFilters\Tests\ContainerFactory;

final class NetteDatabaseFiltersExtensionMultipleContextTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    protected function setUp()
    {
        $this->container = (new ContainerFactory)->createWithConfig(__DIR__ . '/../config/multiple-context.neon');
    }

    public function testContextWasReplaced()
    {
        foreach ($this->container->findByType(Context::class) as $databaseContextServiceName) {
            $databaseContextService = $this->container->getService($databaseContextServiceName);
            $this->assertInstanceOf(FiltersAwareContext::class, $databaseContextService);
        }

        $this->assertCount(2, $this->container->findByType(Context::class));
    }

    public function testFilterManagerWasSet()
    {
        foreach ($this->container->findByType(FiltersAwareContext::class) as $databaseContextServiceName) {
            $databaseContextService = $this->container->getService($databaseContextServiceName);

            $this->assertInstanceOf(
                FilterManagerInterface::class,
                PHPUnit_Framework_Assert::getObjectAttribute($databaseContextService, 'filterManager')
            );
        }
    }
}
