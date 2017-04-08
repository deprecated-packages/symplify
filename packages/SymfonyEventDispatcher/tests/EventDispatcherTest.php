<?php declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\ContainerFactory;

final class EventDispatcherTest extends TestCase
{
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Event was dispatched in subscriber.
     */
    public function test(): void
    {
        $container = (new ContainerFactory)->create();

        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $container->getByType(EventDispatcherInterface::class);
        $eventDispatcher->dispatch('subscriber.event');
    }
}
