<?php declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\NetteEvent;

use Nette\Application\Application;
use Nette\Application\UI\Presenter;
use PHPUnit\Framework\TestCase;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\PresenterShutdownEvent;
use Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\ContainerFactory;

final class DispatchPresenterTest extends TestCase
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
        $containerFactory = (new ContainerFactory)->create();
        $this->application = $containerFactory->getByType(Application::class);
        $this->eventStateStorage = $containerFactory->getByType(EventStateStorage::class);
    }

    public function testDispatch()
    {
        $this->application->run();

        /** @var PresenterShutdownEvent $presenterResponseEvent */
        $presenterResponseEvent = $this->eventStateStorage->getEventState(PresenterShutdownEvent::NAME);
        $this->assertInstanceOf(Presenter::class, $presenterResponseEvent->getPresenter());
        $this->assertNull($presenterResponseEvent->getResponse());
    }
}
