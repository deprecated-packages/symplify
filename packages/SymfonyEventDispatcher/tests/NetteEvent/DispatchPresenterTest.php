<?php

namespace Symplify\SymfonyEventDispatcher\Tests\NetteEvent;

use Nette\Application\Application;
use Nette\Application\UI\Presenter;
use PHPUnit_Framework_TestCase;
use Symplify\SymfonyEventDispatcher\Event\PresenterResponseEvent;
use Symplify\SymfonyEventDispatcher\NettePresenterEvents;
use Symplify\SymfonyEventDispatcher\Tests\ContainerFactory;

final class DispatchPresenterTest extends PHPUnit_Framework_TestCase
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
        $this->application->run();

        /** @var PresenterResponseEvent $presenterResponseEvent */
        $presenterResponseEvent = $this->eventStateStorage->getEventState(NettePresenterEvents::ON_SHUTDOWN);
        $this->assertInstanceOf(Presenter::class, $presenterResponseEvent->getPresenter());
        $this->assertNull($presenterResponseEvent->getResponse());
    }
}
