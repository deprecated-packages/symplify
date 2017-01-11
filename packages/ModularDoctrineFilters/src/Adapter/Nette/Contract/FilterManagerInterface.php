<?php

declare(strict_types=1);

namespace Zenify\DoctrineFilters\Contract;


interface FilterManagerInterface
{

	public function addFilter(string $name, FilterInterface $filter);


	/**
	 * Enables all filters that meet its conditions.
	 */
	public function enableFilters();

}
