<?php

declare(strict_types=1);

namespace Zenify\NetteDatabaseFilters\Contract;

use Nette\Database\Table\Selection;

interface FilterManagerInterface
{

    function addFilter(FilterInterface $filter);


    function applyFilters(Selection $selection, string $targetTable) : Selection;
}
