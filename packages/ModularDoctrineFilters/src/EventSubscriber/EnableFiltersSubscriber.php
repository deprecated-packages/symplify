<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symplify\ModularDoctrineFilters\Contract\FilterManagerInterface;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\PresenterCreatedEvent;

final class EnableFiltersSubscriber implements EventSubscriberInterface
{
    /**
     * @var FilterManagerInterface
     */
    private $filterManager;

    public function setFilterManager(FilterManagerInterface $filterManager): void
    {
        $this->filterManager = $filterManager;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'console.command' => 'enableFilters',
            PresenterCreatedEvent::NAME => 'enableFilters',
            KernelEvents::REQUEST => 'enableFilters'
        ];
    }

    public function enableFilters(): void
    {
        $this->filterManager->enableFilters();
    }
}
