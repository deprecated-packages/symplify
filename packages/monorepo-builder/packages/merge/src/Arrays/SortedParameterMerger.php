<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Arrays;

use Symplify\PackageBuilder\Yaml\ParametersMerger;

final class SortedParameterMerger
{
    /**
     * @var ParametersMerger
     */
    private $parametersMerger;

    /**
     * @var ArraySorter
     */
    private $arraySorter;

    public function __construct(ParametersMerger $parametersMerger, ArraySorter $arraySorter)
    {
        $this->parametersMerger = $parametersMerger;
        $this->arraySorter = $arraySorter;
    }

    /**
     * @return mixed[]
     */
    public function mergeRecursiveAndSort(array $firstArray, array $secondArray): array
    {
        $mergedArray = $this->parametersMerger->mergeWithCombine($firstArray, $secondArray);

        return $this->arraySorter->recursiveSort($mergedArray);
    }

    /**
     * @return mixed[]
     */
    public function mergeAndSort(array $firstArray, array $secondArray): array
    {
        $mergedArray = $this->parametersMerger->merge($firstArray, $secondArray);

        return $this->arraySorter->recursiveSort($mergedArray);
    }
}
