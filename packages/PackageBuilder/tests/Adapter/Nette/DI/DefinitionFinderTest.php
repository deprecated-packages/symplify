<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Adapter\Nette\DI;

use Nette\DI\ContainerBuilder;
use Nette\DI\ServiceDefinition;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionFinder;
use Symplify\PackageBuilder\Exception\DependencyInjection\DefinitionForTypeNotFoundException;

final class DefinitionFinderTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    protected function setUp(): void
    {
        $this->containerBuilder = new ContainerBuilder;
    }

    public function testAutowired(): void
    {
        $definition = new ServiceDefinition;
        $definition->setClass(stdClass::class);

        $this->containerBuilder->addDefinition('some', $definition);

        $this->assertSame($definition, DefinitionFinder::getByType($this->containerBuilder, stdClass::class));
    }

    public function testNonAutowired(): void
    {
        $definition = new ServiceDefinition;
        $definition->setClass(stdClass::class);
        $definition->setAutowired(false);

        $this->containerBuilder->addDefinition('some', $definition);

        $this->assertSame($definition, DefinitionFinder::getByType($this->containerBuilder, stdClass::class));
    }

    public function testMissing(): void
    {
        $this->expectException(DefinitionForTypeNotFoundException::class);
        DefinitionFinder::getByType($this->containerBuilder, stdClass::class);
    }
}
