<?php declare(strict_types=1);

namespace Symplify\DoctrineFilters\Tests\DI;

use Nette\DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symplify\DoctrineFilters\DI\DefinitionFinder;
use Symplify\DoctrineFilters\Exception\DefinitionForTypeNotFoundException;

final class DefinitionFinderTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @var DefinitionFinder
     */
    private $definitionFinder;

    protected function setUp()
    {
        $this->containerBuilder = new ContainerBuilder;
        $this->definitionFinder = new DefinitionFinder($this->containerBuilder);
    }

    public function testAutowired()
    {
        $definition = $this->containerBuilder->addDefinition('some')
            ->setClass(stdClass::class);

        $this->assertSame($definition, $this->definitionFinder->getDefinitionByType(stdClass::class));
    }

    public function testNonAutowired()
    {
        $definition = $this->containerBuilder->addDefinition('some')
            ->setClass(stdClass::class)
            ->setAutowired(false);

        $this->assertSame($definition, $this->definitionFinder->getDefinitionByType(stdClass::class));
    }

    /**
     * @expectedException \Symplify\DoctrineFilters\Exception\DefinitionForTypeNotFoundException
     */
    public function testMissing()
    {
        $this->definitionFinder->getDefinitionByType(stdClass::class);
    }
}
