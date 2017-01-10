<?php declare(strict_types=1);

namespace Zenify\NetteDatabaseFilters;

use Nette\Database\Table\Selection;
use Zenify\NetteDatabaseFilters\Contract\FilterInterface;
use Zenify\NetteDatabaseFilters\Contract\FilterManagerInterface;

final class FilterManager implements FilterManagerInterface
{

    /**
     * @var FilterInterface[]
     */
    private $filters = [];


    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;
    }


    public function applyFilters(Selection $selection, string $targetTable) : Selection
    {
        foreach ($this->filters as $filter) {
            $filter->applyFilter($selection, $targetTable);
        }

        return $selection;
    }
}
