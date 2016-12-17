<?php

namespace Symplify\SymfonyEventDispatcher\Tests\NetteEvent;

use Nette\Application\Application;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;
use PHPUnit_Framework_TestCase;
use Symplify\SymfonyEventDispatcher\Event\ApplicationEvent;
use Symplify\SymfonyEventDispatcher\Event\ApplicationExceptionEvent;
use Symplify\SymfonyEventDispatcher\Event\ApplicationPresenterEvent;
use Symplify\SymfonyEventDispatcher\Event\ApplicationRequestEvent;
use Symplify\SymfonyEventDispatcher\NetteApplicationEvents;
use Symplify\SymfonyEventDispatcher\Tests\ContainerFactory;

final class DispatchApplicationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var EventStateStorage
     */
    private $eventStateStorage;

    protected function setUp()
    {
        $containerFactory = (new ContainerFactory())->create();
        $this->application = $containerFactory->getByType(Application::class);
        $this->eventStateStorage = $containerFactory->getByType(EventStateStorage::class);
    }

    public function testOnRequest()
    {
        $this->application->run();

        /** @var ApplicationRequestEvent $applicationRequestEvent */
        $applicationRequestEvent = $this->eventStateStorage->getEventState(NetteApplicationEvents::ON_REQUEST);
        $this->assertInstanceOf(ApplicationRequestEvent::class, $applicationRequestEvent);
        $this->assertInstanceOf(Application::class, $applicationRequestEvent->getApplication());
        $this->assertInstanceOf(Request::class, $applicationRequestEvent->getRequest());
    }

    public function testOnStartup()
    {
        $this->application->run();

        /** @var ApplicationEvent $applicationEvent */
        $applicationEvent = $this->eventStateStorage->getEventState(NetteApplicationEvents::ON_STARTUP);
        $this->assertInstanceOf(Application::class, $applicationEvent->getApplication());
    }

    public function testOnPresenter()
    {
        $this->application->run();

        /** @var ApplicationPresenterEvent $applicationPresenterEvent */
        $applicationPresenterEvent = $this->eventStateStorage->getEventState(NetteApplicationEvents::ON_PRESENTER);
        $this->assertInstanceOf(Application::class, $applicationPresenterEvent->getApplication());
        $this->assertInstanceOf(Presenter::class, $applicationPresenterEvent->getPresenter());
    }

    public function testOnShutdown()
    {
        $this->application->run();

        /** @var ApplicationExceptionEvent $applicationExceptionEvent */
        $applicationExceptionEvent = $this->eventStateStorage->getEventState(NetteApplicationEvents::ON_SHUTDOWN);
        $this->assertInstanceOf(Application::class, $applicationExceptionEvent->getApplication());
        $this->assertNull($applicationExceptionEvent->getException());
    }
}
