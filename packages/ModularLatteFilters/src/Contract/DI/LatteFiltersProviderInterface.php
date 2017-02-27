<?php declare(strict_types=1);

namespace Zenify\ModularLatteFilters\Contract\DI;

interface LatteFiltersProviderInterface
{
    /**
     * @return callable[]
     */
    public function getFilters(): array;
}
