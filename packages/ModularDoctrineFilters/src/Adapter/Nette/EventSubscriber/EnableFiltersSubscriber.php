<?php

declare(strict_types=1);

namespace Zenify\DoctrineFilters\EventSubscriber;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\PresenterCreatedEvent;
use Zenify\DoctrineFilters\Contract\FilterManagerInterface;


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


	public static function getSubscribedEvents() : array
	{
		return [
			ConsoleEvents::COMMAND => 'enableFilters',
			PresenterCreatedEvent::NAME => 'enableFilters'
		];
	}


	public function enableFilters()
	{
		$this->filterManager->enableFilters();
	}

}
