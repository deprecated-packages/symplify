<?php

declare(strict_types=1);

namespace Symplify\ControllerAutowire\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\ControllerAutowire\DependencyInjection\Compiler\DecorateControllerResolverPass;
use Symplify\ControllerAutowire\DependencyInjection\ControllerClassMap;

final class DecorateControllerResolverPassTest extends TestCase
{
    /**
     * @var ControllerClassMap
     */
    private $controllerClassMap;

    protected function setUp()
    {
        $this->controllerClassMap = new ControllerClassMap();
    }

    public function testInjectionOfOldDecoratedService()
    {
        $containerBuilder = new ContainerBuilder();

        $resolver = new DecorateControllerResolverPass($this->controllerClassMap);
        $resolver->process($containerBuilder);

        $definition = $containerBuilder->getDefinition('symplify.controller_resolver');
        $this->assertSame('symplify.controller_resolver.inner', (string) $definition->getArgument(0));
    }
}
