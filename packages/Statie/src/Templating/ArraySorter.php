<?php declare(strict_types=1);

namespace Symplify\Statie\Templating;

use Symplify\Statie\Exception\Templating\InvalidSortByCriteriaException;
use function Safe\usort;

final class ArraySorter
{
    /**
     * @param mixed[] $items
     * @return mixed[]
     */
    public function sortByField(array $items, string $sortBy, string $direction = 'asc'): array
    {
        if (count($items) < 1) {
            return $items;
        }

        $this->ensureSortByIsValid($items, $sortBy);

        usort($items, function ($a, $b) use ($sortBy, $direction) {
            $inverseConstant = $this->resolveInverseConstant($direction);

            // @todo validate per item with key?
            return ($a[$sortBy] <=> $b[$sortBy]) * $inverseConstant;
        });

        return $items;
    }

    /**
     * @param mixed[]|object[] $items
     */
    private function ensureSortByIsValid(array $items, string $field): void
    {
        $singleItem = current($items);
        if (isset($singleItem[$field])) {
            return;
        }

        throw new InvalidSortByCriteriaException($field, array_keys($singleItem));
    }

    private function resolveInverseConstant(string $direction): int
    {
        $direction = strtolower($direction);

        return in_array($direction, ['d', 'des', 'desc'], true) ? -1 : 1;
    }
}
