<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Arrays;

use Symplify\PackageBuilder\Yaml\ParametersMerger;

final class SortedParameterMerger
{
    public function __construct(
        private ParametersMerger $parametersMerger,
        private ArraySorter $arraySorter
    ) {
    }

    /**
     * @param mixed[] $firstArray
     * @param mixed[] $secondArray
     * @return mixed[]
     */
    public function mergeRecursiveAndSort(array $firstArray, array $secondArray): array
    {
        $mergedArray = $this->parametersMerger->mergeWithCombine($firstArray, $secondArray);

        return $this->arraySorter->recursiveSort($mergedArray);
    }

    /**
     * @param mixed[] $firstArray
     * @param mixed[] $secondArray
     * @return mixed[]
     */
    public function mergeAndSort(array $firstArray, array $secondArray): array
    {
        $mergedArray = $this->parametersMerger->merge($firstArray, $secondArray);

        return $this->arraySorter->recursiveSort($mergedArray);
    }
}
