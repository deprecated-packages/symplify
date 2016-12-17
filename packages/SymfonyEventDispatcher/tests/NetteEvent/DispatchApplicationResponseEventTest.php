<?php

namespace Symplify\SymfonyEventDispatcher\Tests\NetteEvent;

use Nette\Application\Application;
use Nette\Application\IResponse;
use Nette\Application\Request;
use Nette\Application\UI\Component;
use PHPUnit_Framework_TestCase;
use Symplify\SymfonyEventDispatcher\Event\ApplicationResponseEvent;
use Symplify\SymfonyEventDispatcher\NetteApplicationEvents;
use Symplify\SymfonyEventDispatcher\Tests\ContainerFactory;

final class DispatchApplicationResponseEventTest extends PHPUnit_Framework_TestCase
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

    public function testDispatch()
    {
        $this->assertFalse($this->eventStateStorage->getEventState(NetteApplicationEvents::ON_RESPONSE));

        $requestMock = $this->prophesize(Request::class);
        $requestMock->getPresenterName()->willReturn('Response');
        $requestMock->getParameters()->willReturn([]);
        $prefix = class_exists(Component::class) ? '_' : '';
        $requestMock->getPost($prefix . 'do')->willReturn(null);
        $requestMock->isMethod()->willReturn(true);
        $this->application->processRequest($requestMock->reveal());

        /** @var ApplicationResponseEvent $applicationResponseEvent */
        $applicationResponseEvent = $this->eventStateStorage->getEventState(NetteApplicationEvents::ON_RESPONSE);
        $this->assertInstanceOf(Application::class, $applicationResponseEvent->getApplication());
        $this->assertInstanceOf(IResponse::class, $applicationResponseEvent->getResponse());
    }
}
