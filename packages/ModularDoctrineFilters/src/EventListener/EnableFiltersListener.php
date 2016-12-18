<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ModularDoctrineFilters\EventListener;

use Symplify\ModularDoctrineFilters\Contract\Filter\FilterManagerInterface;

final class EnableFiltersListener
{
    /**
     * @var FilterManagerInterface
     */
    private $filterManager;

    public function setFilterManager(FilterManagerInterface $filterManager)
    {
        $this->filterManager = $filterManager;
    }

    public function onKernelRequest()
    {
        $this->filterManager->enableFilters();
    }
}
