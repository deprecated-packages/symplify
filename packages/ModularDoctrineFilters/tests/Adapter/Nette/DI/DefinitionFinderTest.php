<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Tests\Adapter\Nette\DI;

use Nette\DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symplify\ModularDoctrineFilters\Adapter\Nette\DI\DefinitionFinder;

final class DefinitionFinderTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    protected function setUp()
    {
        $this->containerBuilder = new ContainerBuilder;
    }

    public function testAutowired()
    {
        $definition = $this->containerBuilder->addDefinition('some')
            ->setClass(stdClass::class);

        $this->assertSame($definition, DefinitionFinder::getByType($this->containerBuilder, stdClass::class));
    }

    public function testNonAutowired()
    {
        $definition = $this->containerBuilder->addDefinition('some')
            ->setClass(stdClass::class)
            ->setAutowired(false);

        $this->assertSame($definition, DefinitionFinder::getByType($this->containerBuilder, stdClass::class));
    }

    /**
     * @expectedException \Symplify\ModularDoctrineFilters\Exception\DefinitionForTypeNotFoundException
     */
    public function testMissing()
    {
        DefinitionFinder::getByType($this->containerBuilder, stdClass::class);
    }
}
