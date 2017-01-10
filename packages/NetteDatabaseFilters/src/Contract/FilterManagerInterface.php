<?php

declare(strict_types=1);

namespace Zenify\NetteDatabaseFilters\Contract;

use Nette\Database\Table\Selection;

interface FilterManagerInterface
{

    public function addFilter(FilterInterface $filter);


    public function applyFilters(Selection $selection, string $targetTable) : Selection;
}
