<?php

declare(strict_types=1);

namespace Symplify\PHPMDDecomposer\Arrays;

use Symplify\PackageBuilder\Yaml\ParametersMerger;

final class ArrayMerger
{
    /**
     * @var ParametersMerger
     */
    private $parametersMerger;

    public function __construct(ParametersMerger $parametersMerger)
    {
        $this->parametersMerger = $parametersMerger;
    }

    /**
     * @param mixed[] $firstArray
     * @param mixed[] $secondArray
     * @return mixed[]
     */
    public function mergeUnique(array $firstArray, array $secondArray): array
    {
        $mergedArray = $this->parametersMerger->merge($firstArray, $secondArray);
        return array_unique($mergedArray);
    }
}
