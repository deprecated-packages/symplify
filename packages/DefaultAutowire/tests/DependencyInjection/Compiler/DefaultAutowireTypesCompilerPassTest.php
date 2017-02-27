<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\DependencyInjection\Compiler;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symplify\DefaultAutowire\DependencyInjection\Compiler\DefaultAutowireTypesCompilerPass;

final class DefaultAutowireTypesCompilerPassTest extends TestCase
{
    public function testProcess(): void
    {
        $defaultAutowireTypesPass = new DefaultAutowireTypesCompilerPass;
        $containerBuilder = new ContainerBuilder;
        $containerBuilder->setParameter('kernel.root_dir', __DIR__);

        $definition = new Definition(EntityManager::class);
        $containerBuilder->setDefinition('doctrine.orm.default_entity_manager', $definition);

        $this->assertSame([], $definition->getAutowiringTypes());

        $defaultAutowireTypesPass->process($containerBuilder);

        $definition = $containerBuilder->getDefinition('doctrine.orm.default_entity_manager');
        $this->assertSame([EntityManagerInterface::class], $definition->getAutowiringTypes());
    }
}
