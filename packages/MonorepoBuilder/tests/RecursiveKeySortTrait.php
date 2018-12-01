<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests;

use function Safe\ksort;

trait RecursiveKeySortTrait
{
    /**
     * @param mixed[] $array
     */
    private static function recursiveSort(array &$array): void
    {
        if (! is_array($array)) {
            return;
        }

        ksort($array);

        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                self::recursiveSort($value);
            }
        }
    }
}
