<?php

declare(strict_types=1);

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

    /**
     * @var DefinitionFinder
     */
    private $definitionFinder;

    protected function setUp(): void
    {
        $this->containerBuilder = new ContainerBuilder();
        $this->definitionFinder = new DefinitionFinder();
    }

    public function testAutowired(): void
    {
        $definition = $this->containerBuilder->autowire(stdClass::class);

        $this->assertSame($definition, $this->definitionFinder->getByType($this->containerBuilder, stdClass::class));
    }

    public function testNonAutowired(): void
    {
        $definition = $this->containerBuilder->register(stdClass::class);

        $this->assertSame($definition, $this->definitionFinder->getByType($this->containerBuilder, stdClass::class));
    }

    public function testMissing(): void
    {
        $this->expectException(DefinitionForTypeNotFoundException::class);
        $this->definitionFinder->getByType($this->containerBuilder, stdClass::class);
    }
}
