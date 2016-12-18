<?php

declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\NetteEvent\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\PresenterShutdownEvent;
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
        return [PresenterShutdownEvent::NAME => 'onShutdown'];
    }

    public function onShutdown(PresenterShutdownEvent $presenterResponseEvent)
    {
        $this->eventStateStorage->addEventState(PresenterShutdownEvent::NAME, $presenterResponseEvent);
    }
}
