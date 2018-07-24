<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\DependencyInjection\DefinitionCollector;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symplify\PackageBuilder\DependencyInjection\DefinitionCollector;
use Symplify\PackageBuilder\DependencyInjection\DefinitionFinder;
use Symplify\PackageBuilder\Tests\DependencyInjection\DefinitionCollector\Source\Collected;
use Symplify\PackageBuilder\Tests\DependencyInjection\DefinitionCollector\Source\CollectedInterface;
use Symplify\PackageBuilder\Tests\DependencyInjection\DefinitionCollector\Source\Collector;
use Symplify\PackageBuilder\Tests\DependencyInjection\DefinitionCollector\Source\CollectorInterface;

final class DefinitionCollectorTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @var DefinitionCollector
     */
    private $definitionCollector;

    protected function setUp(): void
    {
        $this->containerBuilder = new ContainerBuilder();
        $this->definitionCollector = new DefinitionCollector(new DefinitionFinder());
    }

    public function testLoadCollectorWithType(): void
    {
        $collector = $this->containerBuilder->autowire('first_collector', Collector::class);
        $this->containerBuilder->autowire(Collected::class);

        $secondCollector = $this->containerBuilder->autowire('second_collector', Collector::class);

        $this->definitionCollector->loadCollectorWithType(
            $this->containerBuilder,
            CollectorInterface::class,
            CollectedInterface::class,
            'addCollected'
        );

        $methodCalls = $collector->getMethodCalls();
        $this->assertCount(1, $methodCalls);

        $adderStatement = $methodCalls[0];
        $this->assertSame('addCollected', $adderStatement[0]);
        $arguments = $adderStatement[1];
        $this->assertInstanceOf(Reference::class, $arguments[0]);
        $this->assertSame(Collected::class, (string) $arguments[0]);

        $methodCalls = $secondCollector->getMethodCalls();
        $this->assertCount(1, $methodCalls);
    }
}
