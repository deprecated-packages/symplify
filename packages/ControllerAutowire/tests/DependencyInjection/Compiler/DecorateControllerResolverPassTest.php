<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\ControllerAutowire\DependencyInjection\Compiler\DecorateControllerResolverPass;
use Symplify\ControllerAutowire\HttpKernel\Controller\ControllerResolver;

final class DecorateControllerResolverPassTest extends TestCase
{
    public function testInjectionOfOldDecoratedService(): void
    {
        $containerBuilder = new ContainerBuilder;

        $resolver = new DecorateControllerResolverPass;
        $resolver->process($containerBuilder);

        $definition = $containerBuilder->getDefinition(ControllerResolver::class);
        $this->assertSame(ControllerResolver::class . '.inner', (string) $definition->getArgument(0));
    }
}
