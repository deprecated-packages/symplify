<?php

declare(strict_types=1);

namespace Symplify\PHPMDDecomposer\ValueObject\Config;

use Symplify\PackageBuilder\Yaml\ParametersMerger;
use Symplify\PHPMDDecomposer\Arrays\ArrayMerger;

abstract class AbstractConfig
{
    /**
     * @var ArrayMerger
     */
    private $arrayMerger;

    public function __construct()
    {
        $parametersMerger = new ParametersMerger();
        $this->arrayMerger = new ArrayMerger($parametersMerger);
    }

    /**
     * @param mixed[] $firstArray
     * @param mixed[] $secondArray
     * @return mixed[]
     */
    protected function mergeUnique(array $firstArray, array $secondArray): array
    {
        return $this->arrayMerger->mergeUnique($firstArray, $secondArray);
    }
}
