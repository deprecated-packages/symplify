<?php declare(strict_types=1);

namespace Symplify\Statie\Templating;

use Symplify\Statie\Exception\Templating\InvalidSortByCriteriaException;

final class ArrayUtils
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

        $this->ensureKeyIsValid($items, $sortBy);

        usort($items, function ($firstItem, $secondItem) use ($sortBy, $direction): int {
            $inverseConstant = $this->resolveInverseConstant($direction);

            $firstValue = $firstItem[$sortBy];
            $secondValue = $secondItem[$sortBy];

            if (is_string($firstValue)) {
                $firstValue = strtolower($firstValue);
            }

            if (is_string($secondValue)) {
                $secondValue = strtolower($secondValue);
            }

            return ($firstValue <=> $secondValue) * $inverseConstant;
        });

        return $items;
    }

    /**
     * @param mixed[] $items
     * @return mixed[]
     */
    public function groupByField(array $items, string $groupBy): array
    {
        if (count($items) < 1) {
            return $items;
        }

        $this->ensureKeyIsValid($items, $groupBy);

        $grouped = [];
        foreach ($items as $item) {
            $grouped[$item[$groupBy]][] = $item;
        }

        ksort($grouped);

        return $grouped;
    }

    /**
     * @param mixed[]|object[] $items
     */
    private function ensureKeyIsValid(array $items, string $field): void
    {
        foreach ($items as $item) {
            if (isset($item[$field])) {
                continue;
            }

            throw new InvalidSortByCriteriaException($field, array_keys($item));
        }
    }

    private function resolveInverseConstant(string $direction): int
    {
        $direction = strtolower($direction);

        return in_array($direction, ['d', 'des', 'desc'], true) ? -1 : 1;
    }
}
