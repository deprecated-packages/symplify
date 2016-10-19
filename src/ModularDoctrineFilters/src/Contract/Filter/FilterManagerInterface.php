<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ModularDoctrineFilters\Contract\Filter;

interface FilterManagerInterface
{
    public function addFilter(string $name, FilterInterface $filter);

    /**
     * Enables filters for EntityManager.
     */
    public function enableFilters();
}
