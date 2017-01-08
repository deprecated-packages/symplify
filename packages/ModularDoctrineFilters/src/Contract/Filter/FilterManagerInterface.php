<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Contract\Filter;

interface FilterManagerInterface
{
    public function addFilter(string $name, FilterInterface $filter);

    /**
     * Enables filters for EntityManager.
     */
    public function enableFilters();
}
