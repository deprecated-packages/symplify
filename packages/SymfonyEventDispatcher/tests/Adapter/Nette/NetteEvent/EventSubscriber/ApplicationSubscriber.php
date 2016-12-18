<?php

declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\NetteEvent\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationExceptionEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationPresenterEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationRequestEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationResponseEvent;
use Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\NetteEvent\EventStateStorage;

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
            ApplicationRequestEvent::ON_REQUEST => 'onRequest',
            ApplicationEvent::ON_STARTUP => 'onStartup',
            ApplicationPresenterEvent::ON_PRESENTER => 'onPresenter',
            ApplicationExceptionEvent::ON_SHUTDOWN => 'onShutdown',
            ApplicationResponseEvent::ON_RESPONSE => 'onResponse',
            ApplicationExceptionEvent::ON_ERROR => 'onError',
        ];
    }

    public function onRequest(ApplicationRequestEvent $applicationRequestEvent)
    {
        $this->eventStateStorage->addEventState(ApplicationRequestEvent::ON_REQUEST, $applicationRequestEvent);
    }

    public function onStartup(ApplicationEvent $applicationEvent)
    {
        $this->eventStateStorage->addEventState(ApplicationEvent::ON_STARTUP, $applicationEvent);
    }

    public function onPresenter(ApplicationPresenterEvent $applicationPresenterEvent)
    {
        $this->eventStateStorage->addEventState(ApplicationPresenterEvent::ON_PRESENTER, $applicationPresenterEvent);
    }

    public function onShutdown(ApplicationExceptionEvent $applicationExceptionEvent)
    {
        $this->eventStateStorage->addEventState(ApplicationExceptionEvent::ON_SHUTDOWN, $applicationExceptionEvent);
    }

    public function onError(ApplicationExceptionEvent $applicationExceptionEvent)
    {
        $this->eventStateStorage->addEventState(ApplicationExceptionEvent::ON_ERROR, $applicationExceptionEvent);
    }

    public function onResponse(ApplicationResponseEvent $applicationResponseEvent)
    {
        $this->eventStateStorage->addEventState(ApplicationResponseEvent::ON_RESPONSE, $applicationResponseEvent);
    }
}
