<?php

declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\NetteEvent\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\PresenterResponseEvent;
use Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\NetteEvent\EventStateStorage;

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
        return [PresenterResponseEvent::ON_SHUTDOWN => 'onShutdown'];
    }

    public function onShutdown(PresenterResponseEvent $presenterResponseEvent)
    {
        $this->eventStateStorage->addEventState(PresenterResponseEvent::ON_SHUTDOWN, $presenterResponseEvent);
    }
}
