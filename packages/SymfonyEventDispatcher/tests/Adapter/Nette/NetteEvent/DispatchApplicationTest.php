<?php declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\NetteEvent;

use Nette\Application\Application;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;
use PHPUnit\Framework\TestCase;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationErrorEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationStartupEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\PresenterCreatedEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\RequestRecievedEvent;
use Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\ContainerFactory;

final class DispatchApplicationTest extends TestCase
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var EventStateStorage
     */
    private $eventStateStorage;

    protected function setUp(): void
    {
        $containerFactory = (new ContainerFactory)->create();
        $this->application = $containerFactory->getByType(Application::class);
        $this->eventStateStorage = $containerFactory->getByType(EventStateStorage::class);
    }

    public function testOnRequest(): void
    {
        $this->application->run();

        /** @var RequestRecievedEvent $applicationRequestEvent */
        $applicationRequestEvent = $this->eventStateStorage->getEventState(RequestRecievedEvent::NAME);
        $this->assertInstanceOf(RequestRecievedEvent::class, $applicationRequestEvent);
        $this->assertInstanceOf(Application::class, $applicationRequestEvent->getApplication());
        $this->assertInstanceOf(Request::class, $applicationRequestEvent->getRequest());
    }

    public function testOnStartup(): void
    {
        $this->application->run();

        /** @var ApplicationStartupEvent $applicationEvent */
        $applicationEvent = $this->eventStateStorage->getEventState(ApplicationStartupEvent::NAME);
        $this->assertInstanceOf(Application::class, $applicationEvent->getApplication());
    }

    public function testOnPresenter(): void
    {
        $this->application->run();

        /** @var PresenterCreatedEvent $applicationPresenterEvent */
        $applicationPresenterEvent = $this->eventStateStorage->getEventState(PresenterCreatedEvent::NAME);
        $this->assertInstanceOf(Application::class, $applicationPresenterEvent->getApplication());
        $this->assertInstanceOf(Presenter::class, $applicationPresenterEvent->getPresenter());
    }

    public function testOnShutdown(): void
    {
        $this->application->run();

        /** @var ApplicationErrorEvent $applicationExceptionEvent */
        $applicationExceptionEvent = $this->eventStateStorage->getEventState(ApplicationErrorEvent::NAME);
        $this->assertInstanceOf(Application::class, $applicationExceptionEvent->getApplication());
        $this->assertNull($applicationExceptionEvent->getException());
    }
}
