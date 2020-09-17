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
     * @param string[] $strings
     * @param int[] $integers
     * @param bool[] $booleans
     * @param float[] $floats
     */
    public function __construct(array $strings, array $integers, array $booleans, array $floats)
    {
        $this->strings = $strings;
        $this->integers = $integers;
        $this->booleans = $booleans;
        $this->floats = $floats;
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
}
