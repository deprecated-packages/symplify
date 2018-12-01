<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests;

use function Safe\ksort;

final class ArraySorter
{
    /**
     * @param mixed $array
     * @return mixed
     */
    public function recursiveSort($array)
    {
        if (! is_array($array)) {
            return $array;
        }

        ksort($array);

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->recursiveSort($value);
            }
        }

        return $array;
    }
}
