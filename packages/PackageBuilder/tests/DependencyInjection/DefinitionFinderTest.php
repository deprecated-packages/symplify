<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\DependencyInjection\DefinitionFinder;
use Symplify\PackageBuilder\Exception\DependencyInjection\DefinitionForTypeNotFoundException;

final class DefinitionFinderTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    protected function setUp(): void
    {
        $this->containerBuilder = new ContainerBuilder();
    }

    public function testAutowired(): void
    {
        $definition = $this->containerBuilder->autowire(stdClass::class);

        $this->assertSame($definition, DefinitionFinder::getByType($this->containerBuilder, stdClass::class));
    }

    public function testNonAutowired(): void
    {
        $definition = $this->containerBuilder->register(stdClass::class);

        $this->assertSame($definition, DefinitionFinder::getByType($this->containerBuilder, stdClass::class));
    }

    public function testMissing(): void
    {
        $this->expectException(DefinitionForTypeNotFoundException::class);
        DefinitionFinder::getByType($this->containerBuilder, stdClass::class);
    }
}
