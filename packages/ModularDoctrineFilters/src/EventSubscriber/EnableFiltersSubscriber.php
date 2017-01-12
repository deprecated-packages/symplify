<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\EventSubscriber;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symplify\ModularDoctrineFilters\Contract\Filter\FilterManagerInterface;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\PresenterCreatedEvent;

final class EnableFiltersSubscriber implements EventSubscriberInterface
{
    /**
     * @var FilterManagerInterface
     */
    private $filterManager;

    public function setFilterManager(FilterManagerInterface $filterManager)
    {
        $this->filterManager = $filterManager;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents() : array
    {
        return [
            ConsoleEvents::COMMAND => 'enableFilters',
            PresenterCreatedEvent::NAME => 'enableFilters',
            KernelEvents::REQUEST => 'enableFilters'
        ];
    }

    public function enableFilters()
    {
        $this->filterManager->enableFilters();
    }
}
