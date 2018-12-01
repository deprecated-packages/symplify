<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests;

use function Safe\ksort;

trait RecursiveKeySortTrait
{
    /**
     * @param mixed $array
     * @return mixed
     */
    private function recursiveSort($array)
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
