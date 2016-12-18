<?php

declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\NetteEvent\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationStartupEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationErrorEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\PresenterCreatedEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\RequestRecievedEvent;
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
            RequestRecievedEvent::NAME => 'onRequest',
            ApplicationStartupEvent::NAME => 'onStartup',
            PresenterCreatedEvent::NAME => 'onPresenter',
            ApplicationErrorEvent::NAME => 'onShutdown',
            ApplicationResponseEvent::NAME => 'onResponse',
            ApplicationErrorEvent::NAME => 'onError',
        ];
    }

    public function onRequest(RequestRecievedEvent $applicationRequestEvent)
    {
        $this->eventStateStorage->addEventState(RequestRecievedEvent::NAME, $applicationRequestEvent);
    }

    public function onStartup(ApplicationStartupEvent $applicationEvent)
    {
        $this->eventStateStorage->addEventState(ApplicationStartupEvent::NAME, $applicationEvent);
    }

    public function onPresenter(PresenterCreatedEvent $applicationPresenterEvent)
    {
        $this->eventStateStorage->addEventState(PresenterCreatedEvent::NAME, $applicationPresenterEvent);
    }

    public function onShutdown(ApplicationErrorEvent $applicationExceptionEvent)
    {
        $this->eventStateStorage->addEventState(ApplicationErrorEvent::NAME, $applicationExceptionEvent);
    }

    public function onError(ApplicationErrorEvent $applicationExceptionEvent)
    {
        $this->eventStateStorage->addEventState(ApplicationErrorEvent::NAME, $applicationExceptionEvent);
    }

    public function onResponse(ApplicationResponseEvent $applicationResponseEvent)
    {
        $this->eventStateStorage->addEventState(ApplicationResponseEvent::NAME, $applicationResponseEvent);
    }
}
