<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Tests\EventSubscriber;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\Application;
use Nette\Application\IPresenter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\ModularDoctrineFilters\Tests\Adapter\Nette\ContainerFactory;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\PresenterCreatedEvent;

final class EnableFiltersSubscriberTest extends TestCase
{
    public function test()
    {
        $container = (new ContainerFactory)->create();
        $eventDispatcher = $container->getByType(EventDispatcherInterface::class);
        /** @var EntityManager $entityManager */
        $entityManager = $container->getByType(EntityManagerInterface::class);

        $filters = $entityManager->getFilters();

        $this->assertCount(0, $filters->getEnabledFilters());

        $eventDispatcher->dispatch(PresenterCreatedEvent::NAME, $this->createApplicationPresenterEvent());

        $this->assertCount(2, $filters->getEnabledFilters());
    }

    private function createApplicationPresenterEvent() : PresenterCreatedEvent
    {
        $applicationMock = $this->prophesize(Application::class);
        $presenterMock = $this->prophesize(IPresenter::class);
        $applicationPresenterEvent = new PresenterCreatedEvent(
            $applicationMock->reveal(),
            $presenterMock->reveal()
        );
        return $applicationPresenterEvent;
    }
}
