<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Arrays;

final class ArraySorter
{
    /**
     * @param mixed[] $array
     * @return mixed[]
     */
    public function recursiveSort(array $array): array
    {
        if ($array === []) {
            return $array;
        }

        if ($this->isSequential($array)) {
            sort($array);
        } else {
            ksort($array);
        }

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->recursiveSort($value);
            }
        }

        return $array;
    }

    /**
     * @param mixed[] $array
     */
    private function isSequential(array $array): bool
    {
        return array_keys($array) === range(0, count($array) - 1);
    }
}
