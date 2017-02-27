<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Contract;

use Symplify\ModularDoctrineFilters\Contract\Filter\FilterInterface;

interface FilterManagerInterface
{
    public function addFilter(string $name, FilterInterface $filter): void;

    /**
     * Enables filters for EntityManager.
     */
    public function enableFilters(): void;
}
