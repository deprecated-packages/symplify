<?php declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\NetteEvent;

use Nette\Application\Application;
use Nette\Application\IResponse;
use Nette\Application\Request;
use Nette\Application\UI\Component;
use PHPUnit\Framework\TestCase;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationResponseEvent;
use Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\ContainerFactory;

final class DispatchApplicationResponseEventTest extends TestCase
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
        $this->assertFalse((bool) $this->eventStateStorage->getEventState(ApplicationResponseEvent::NAME));

        $requestMock = $this->prophesize(Request::class);
        $requestMock->getPresenterName()->willReturn('Response');
        $requestMock->getParameters()->willReturn([]);
        $prefix = class_exists(Component::class) ? '_' : '';
        $requestMock->getPost($prefix . 'do')->willReturn(null);
        $requestMock->isMethod('FORWARD')->willReturn(true);
        $this->application->processRequest($requestMock->reveal());

        /** @var ApplicationResponseEvent $applicationResponseEvent */
        $applicationResponseEvent = $this->eventStateStorage->getEventState(ApplicationResponseEvent::NAME);
        $this->assertInstanceOf(Application::class, $applicationResponseEvent->getApplication());
        $this->assertInstanceOf(IResponse::class, $applicationResponseEvent->getResponse());
    }
}
