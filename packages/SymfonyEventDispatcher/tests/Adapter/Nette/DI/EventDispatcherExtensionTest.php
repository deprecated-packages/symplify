<?php declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\DI;

use Nette\DI\Compiler;
use Nette\DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\DI\SymfonyEventDispatcherExtension;
use Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\DI\EventDispatcherExtensionSource\SomeEventSubscriber;

final class EventDispatcherExtensionTest extends TestCase
{
    public function testGetEventDispatcher(): void
    {
        $extension = $this->getExtension();
        $extension->loadConfiguration();

        $containerBuilder = $extension->getContainerBuilder();

        // Emulates life cycle from Nette\DI\Compiler.
        // @link https://github.com/nette/di/blob/9b0f8150f823ca22813ba9871156dabb20a911fa/src/DI/Compiler.php#L202-L207
        $containerBuilder->prepareClassList();

        $eventDispatcherDefinition = $containerBuilder->getDefinition(
            $containerBuilder->getByType(EventDispatcherInterface::class)
        );
        $this->assertSame(EventDispatcher::class, $eventDispatcherDefinition->getClass());
    }

    public function testLoadSubscribers(): void
    {
        $extension = $this->getExtension();
        $containerBuilder = $extension->getContainerBuilder();

        $extension->loadConfiguration();

        $containerBuilder->addDefinition('eventSubscriber')
            ->setClass(SomeEventSubscriber::class);

        // Emulates life cycle from Nette\DI\Compiler.
        // @link https://github.com/nette/di/blob/9b0f8150f823ca22813ba9871156dabb20a911fa/src/DI/Compiler.php#L202-L207
        $containerBuilder->prepareClassList();

        $extension->beforeCompile();

        $eventDispatcherDefinition = $containerBuilder->getDefinition(
            $containerBuilder->getByType(EventDispatcherInterface::class)
        );

        $this->assertCount(1, $eventDispatcherDefinition->getSetup());
    }

    private function getExtension(): SymfonyEventDispatcherExtension
    {
        $extension = new SymfonyEventDispatcherExtension;
        $extension->setCompiler(new Compiler(new ContainerBuilder), 'events');

        return $extension;
    }
}
