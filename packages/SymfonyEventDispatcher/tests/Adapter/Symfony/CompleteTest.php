<?php declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Symfony;

use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\SymfonyEventDispatcher\Tests\Adapter\Symfony\Event\SomeEvent;

final class CompleteTest extends TestCase
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    protected function setUp(): void
    {
        $kernel = new AppKernel;
        $kernel->boot();

        $container = $kernel->getContainer();

        $this->eventDispatcher = $container->get('event_dispatcher');
    }

    public function test(): void
    {
        $event = new SomeEvent;
        $this->assertSame('off', $event->getState());

        $this->eventDispatcher->dispatch(SomeEvent::class, $event);
        $this->assertSame('on', $event->getState());
    }
}
