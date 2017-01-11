<?php

declare(strict_types=1);

namespace Zenify\DoctrineFilters;

use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Zenify\DoctrineFilters\Contract\ConditionalFilterInterface;
use Zenify\DoctrineFilters\Contract\FilterInterface;
use Zenify\DoctrineFilters\Contract\FilterManagerInterface;


final class FilterManager implements FilterManagerInterface
{

	/**
	 * @var FilterInterface[]
	 */
	private $filters = [];

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * @var bool
	 */
	private $areFiltersEnabled = FALSE;


	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}


	public function addFilter(string $name, FilterInterface $filter)
	{
		$this->filters[$name] = $filter;
	}


	public function enableFilters()
	{
		if ($this->areFiltersEnabled) {
			return;
		}

		foreach ($this->filters as $name => $filter) {
			if ($filter instanceof ConditionalFilterInterface && ! $filter->isEnabled()) {
				continue;
			}

			$this->addFilterToEnabledInFilterCollection($name, $filter);
		}

		$this->areFiltersEnabled = TRUE;
	}


	private function addFilterToEnabledInFilterCollection(string $name, FilterInterface $filter)
	{
		$filterCollection = $this->entityManager->getFilters();

		$filterCollectionReflection = new ReflectionClass($filterCollection);
		$enabledFiltersReflection = $filterCollectionReflection->getProperty('enabledFilters');
		$enabledFiltersReflection->setAccessible(TRUE);

		$enabledFilters = $enabledFiltersReflection->getValue($filterCollection);
		$enabledFilters[$name] = $filter;
		$enabledFiltersReflection->setValue($filterCollection, $enabledFilters);
	}

}
