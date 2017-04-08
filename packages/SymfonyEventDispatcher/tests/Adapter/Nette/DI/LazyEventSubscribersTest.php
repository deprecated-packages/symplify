<?php declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\DI;

use Nette\DI\Container;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\ContainerFactory;
use Throwable;

final class LazyEventSubscribersTest extends TestCase
{
    public function test(): void
    {
        $container = (new ContainerFactory)->createWithConfig(__DIR__ . '/../config/lazySubscribers.neon');

        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $container->getByType(EventDispatcherInterface::class);

        $this->ensureSubscriberIsInitializedOnlyWhenNeeded($container, $eventDispatcher);
    }

    private function ensureSubscriberIsInitializedOnlyWhenNeeded(
        Container $container,
        EventDispatcherInterface $eventDispatcher
    ): void {
        $this->assertFalse($container->isCreated('subscriber'));
        try {
            $eventDispatcher->dispatch('subscriber.event');
        } catch (Throwable $exception) {
            $this->assertSame('Event was dispatched in subscriber.', $exception->getMessage());
        }
        $this->assertTrue($container->isCreated('subscriber'));
    }
}
