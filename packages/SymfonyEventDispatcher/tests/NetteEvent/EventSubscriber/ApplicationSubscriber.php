<?php

declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\NetteEvent\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\SymfonyEventDispatcher\Event\ApplicationEvent;
use Symplify\SymfonyEventDispatcher\Event\ApplicationExceptionEvent;
use Symplify\SymfonyEventDispatcher\Event\ApplicationPresenterEvent;
use Symplify\SymfonyEventDispatcher\Event\ApplicationRequestEvent;
use Symplify\SymfonyEventDispatcher\Event\ApplicationResponseEvent;
use Symplify\SymfonyEventDispatcher\NetteApplicationEvents;
use Symplify\SymfonyEventDispatcher\Tests\NetteEvent\EventStateStorage;

final class ApplicationSubscriber implements EventSubscriberInterface
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
        return [
            NetteApplicationEvents::ON_REQUEST => 'onRequest',
            NetteApplicationEvents::ON_STARTUP => 'onStartup',
            NetteApplicationEvents::ON_PRESENTER => 'onPresenter',
            NetteApplicationEvents::ON_SHUTDOWN => 'onShutdown',
            NetteApplicationEvents::ON_RESPONSE => 'onResponse',
            NetteApplicationEvents::ON_ERROR => 'onError',
        ];
    }

    public function onRequest(ApplicationRequestEvent $applicationRequestEvent)
    {
        $this->eventStateStorage->addEventState(NetteApplicationEvents::ON_REQUEST, $applicationRequestEvent);
    }

    public function onStartup(ApplicationEvent $applicationEvent)
    {
        $this->eventStateStorage->addEventState(NetteApplicationEvents::ON_STARTUP, $applicationEvent);
    }

    public function onPresenter(ApplicationPresenterEvent $applicationPresenterEvent)
    {
        $this->eventStateStorage->addEventState(NetteApplicationEvents::ON_PRESENTER, $applicationPresenterEvent);
    }

    public function onShutdown(ApplicationExceptionEvent $applicationExceptionEvent)
    {
        $this->eventStateStorage->addEventState(NetteApplicationEvents::ON_SHUTDOWN, $applicationExceptionEvent);
    }

    public function onError(ApplicationExceptionEvent $applicationExceptionEvent)
    {
        $this->eventStateStorage->addEventState(NetteApplicationEvents::ON_ERROR, $applicationExceptionEvent);
    }

    public function onResponse(ApplicationResponseEvent $applicationResponseEvent)
    {
        $this->eventStateStorage->addEventState(NetteApplicationEvents::ON_RESPONSE, $applicationResponseEvent);
    }
}
