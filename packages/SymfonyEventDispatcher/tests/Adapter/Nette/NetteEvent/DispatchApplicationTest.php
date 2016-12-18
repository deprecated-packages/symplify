<?php

declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\NetteEvent;

use Nette\Application\Application;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;
use PHPUnit_Framework_TestCase;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationExceptionEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationPresenterEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationRequestEvent;
use Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\ContainerFactory;

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
        $applicationRequestEvent = $this->eventStateStorage->getEventState(ApplicationRequestEvent::ON_REQUEST);
        $this->assertInstanceOf(ApplicationRequestEvent::class, $applicationRequestEvent);
        $this->assertInstanceOf(Application::class, $applicationRequestEvent->getApplication());
        $this->assertInstanceOf(Request::class, $applicationRequestEvent->getRequest());
    }

    public function testOnStartup()
    {
        $this->application->run();

        /** @var ApplicationEvent $applicationEvent */
        $applicationEvent = $this->eventStateStorage->getEventState(ApplicationEvent::ON_STARTUP);
        $this->assertInstanceOf(Application::class, $applicationEvent->getApplication());
    }

    public function testOnPresenter()
    {
        $this->application->run();

        /** @var ApplicationPresenterEvent $applicationPresenterEvent */
        $applicationPresenterEvent = $this->eventStateStorage->getEventState(ApplicationPresenterEvent::ON_PRESENTER);
        $this->assertInstanceOf(Application::class, $applicationPresenterEvent->getApplication());
        $this->assertInstanceOf(Presenter::class, $applicationPresenterEvent->getPresenter());
    }

    public function testOnShutdown()
    {
        $this->application->run();

        /** @var ApplicationExceptionEvent $applicationExceptionEvent */
        $applicationExceptionEvent = $this->eventStateStorage->getEventState(ApplicationExceptionEvent::ON_SHUTDOWN);
        $this->assertInstanceOf(Application::class, $applicationExceptionEvent->getApplication());
        $this->assertNull($applicationExceptionEvent->getException());
    }
}
