<?php

declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Symfony;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\ModularDoctrineFilters\Tests\Controller\SomeController;
use Symplify\SymfonyEventDispatcher\Tests\Adapter\Symfony\Event\SomeEvent;

final class CompleteTest extends TestCase
{
    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    protected function setUp()
    {
        $kernel = new AppKernel();
        $kernel->boot();

        $this->eventDispatcher = $kernel->getContainer()
            ->get('symplify.event_dispatcher');
    }

    public function test()
    {
        $event = new SomeEvent();
        $this->assertSame('off', $event->getState());

        $this->eventDispatcher->dispatch(SomeEvent::NAME, $event);
        $this->assertSame('on', $event->getState());
    }
}
