<?php

declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\NetteEvent\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\SymfonyEventDispatcher\Event\PresenterResponseEvent;
use Symplify\SymfonyEventDispatcher\NettePresenterEvents;
use Symplify\SymfonyEventDispatcher\Tests\NetteEvent\EventStateStorage;

final class PresenterSubscriber implements EventSubscriberInterface
{
    /**
     * @var EventStateStorage
     */
    private $eventStateStorage;

    public function __construct(EventStateStorage $eventStateStorage)
    {
        $this->eventStateStorage = $eventStateStorage;
    }

    public static function getSubscribedEvents() : array
    {
        return [NettePresenterEvents::ON_SHUTDOWN => 'onShutdown'];
    }

    public function onShutdown(PresenterResponseEvent $presenterResponseEvent)
    {
        $this->eventStateStorage->addEventState(NettePresenterEvents::ON_SHUTDOWN, $presenterResponseEvent);
    }
}
