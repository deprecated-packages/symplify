<?php declare(strict_types=1);

namespace Zenify\NetteDatabaseFilters\Contract;

use Nette\Database\Table\Selection;

interface FilterInterface
{

    public function applyFilter(Selection $selection, string $targetTable);
}
