<?php

declare(strict_types=1);

namespace Zenify\DoctrineFilters\Contract;

interface ConditionalFilterInterface extends FilterInterface
{
    /**
     * Resolves conditions that are required to enable filter.
     * Filters are active by default.
     */
    public function isEnabled() : bool;
}
