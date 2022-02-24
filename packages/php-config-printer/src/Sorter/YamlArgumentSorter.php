<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Sorter;

final class YamlArgumentSorter
{
    /**
     * @param mixed[] $arrayToSort
     * @param array<string, mixed> $inOrderKeys Pass array of keys to sort if exists or an associative array following this logic [$key => $valueIfNotExists]
     *
     * @return mixed[]
     */
    public function sortArgumentsByKeyIfExists(array $arrayToSort, array $inOrderKeys): array
    {
        $argumentsInOrder = [];

        if ($this->isAssociativeArray($inOrderKeys)) {
            foreach ($inOrderKeys as $key => $valueIfNotExists) {
                $argumentsInOrder[$key] = $arrayToSort[$key] ?? $valueIfNotExists;
            }

            return $argumentsInOrder;
        }

        foreach ($inOrderKeys as $inOrderKey) {
            if (isset($arrayToSort[$inOrderKey])) {
                $argumentsInOrder[] = $arrayToSort[$inOrderKey];
            }
        }

        return $argumentsInOrder;
    }

    /**
     * @param mixed[] $array
     */
    private function isAssociativeArray(array $array): bool
    {
        $zeroToItemCount = range(0, count($array) - 1);
        return array_keys($array) !== $zeroToItemCount;
    }
}
