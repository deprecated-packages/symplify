<?php declare (strict_types=1);

namespace Symplify\EasyHydrator\Tests\Fixture;

final class Arrays
{
    /**
     * @var string[]
     */
    private $strings;

    /**
     * @var int[]
     */
    private $integers;

    /**
     * @var float[]
     */
    private $floats;

    /**
     * @var bool[]
     */
    private $booleans;

    /**
     * @var int[][][];
     */
    private $arrayOfArrays;

    /**
     * @param string[] $strings
     * @param int[] $integers
     * @param bool[] $booleans
     * @param float[] $floats
     * @param int[][][] $arrayOfArrays
     */
    public function __construct(array $strings, array $integers, array $booleans, array $floats, array $arrayOfArrays)
    {
        $this->strings = $strings;
        $this->integers = $integers;
        $this->booleans = $booleans;
        $this->floats = $floats;
        $this->arrayOfArrays = $arrayOfArrays;
    }

    public function getStrings(): array
    {
        return $this->strings;
    }

    public function getIntegers(): array
    {
        return $this->integers;
    }

    public function getFloats(): array
    {
        return $this->floats;
    }

    public function getBooleans(): array
    {
        return $this->booleans;
    }

    /**
     * @return int[][][]
     */
    public function getArrayOfArrays(): array
    {
        return $this->arrayOfArrays;
    }
}
