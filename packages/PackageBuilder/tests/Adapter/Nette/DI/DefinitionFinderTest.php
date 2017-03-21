<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Adapter\Nette\DI;

use Nette\DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionFinder;

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
        $definition = $this->containerBuilder->addDefinition('some')
            ->setClass(stdClass::class);

        $this->assertSame($definition, DefinitionFinder::getByType($this->containerBuilder, stdClass::class));
    }

    public function testNonAutowired(): void
    {
        $definition = $this->containerBuilder->addDefinition('some')
            ->setClass(stdClass::class)
            ->setAutowired(false);

        $this->assertSame($definition, DefinitionFinder::getByType($this->containerBuilder, stdClass::class));
    }

    /**
     * @expectedException \Symplify\PackageBuilder\Exception\DependencyInjection\DefinitionForTypeNotFoundException
     */
    public function testMissing(): void
    {
        DefinitionFinder::getByType($this->containerBuilder, stdClass::class);
    }
}
