<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Adapter\Nette\DI;

use Nette\DI\ContainerBuilder;
use Nette\DI\Statement;
use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;
use Symplify\PackageBuilder\Tests\Adapter\Source\Collected;
use Symplify\PackageBuilder\Tests\Adapter\Source\CollectedInterface;
use Symplify\PackageBuilder\Tests\Adapter\Source\Collector;
use Symplify\PackageBuilder\Tests\Adapter\Source\CollectorInterface;

final class DefinitionCollectorTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    protected function setUp(): void
    {
        $this->containerBuilder = new ContainerBuilder;
    }

    public function testLoadCollectorWithType(): void
    {
        $collectorDefinition = $this->containerBuilder->addDefinition('collector_name');
        $collectorDefinition->setClass(Collector::class);

        $collectedDefinition = $this->containerBuilder->addDefinition('collected_name');
        $collectedDefinition->setClass(Collected::class);

        DefinitionCollector::loadCollectorWithType(
            $this->containerBuilder,
            CollectorInterface::class,
            CollectedInterface::class,
            'addCollected'
        );

        $this->assertCount(1, $collectorDefinition->getSetup());
        $adderStatement = $collectorDefinition->getSetup()[0];
        $this->assertInstanceOf(Statement::class, $adderStatement);
        $this->assertSame('addCollected', $adderStatement->getEntity());
        $this->assertSame(['@collected_name'], $adderStatement->arguments);
    }
}
