<?php declare(strict_types=1);

namespace Zenify\NetteDatabaseFilters\Tests\Filter;

use Nette\Database\Table\Selection;
use Zenify\NetteDatabaseFilters\Contract\FilterInterface;

final class IgnoreCommentsByJakubFilter implements FilterInterface
{
    public function applyFilter(Selection $selection, string $targetTable)
    {
        if ($targetTable !== 'comment') {
            return;
        }

        $selection->where('name != ?', 'Jakub');
    }
}
