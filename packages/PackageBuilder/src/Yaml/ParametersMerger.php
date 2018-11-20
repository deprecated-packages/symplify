<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Yaml;

final class ParametersMerger
{
    /**
     * Merges configurations. Left has higher priority than right one.
     *
     * @autor David Grudl (https://davidgrudl.com)
     * @source https://github.com/nette/di/blob/8eb90721a131262f17663e50aee0032a62d0ef08/src/DI/Config/Helpers.php#L31
     *
     * @param mixed $left
     * @param mixed $right
     * @return mixed[]|string
     */
    public function merge($left, $right)
    {
        if (is_array($left) && is_array($right)) {
            foreach ($left as $key => $val) {
                if (is_int($key)) {
                    $right[] = $val;
                } else {
                    if (isset($right[$key])) {
                        $val = $this->merge($val, $right[$key]);
                    }
                    $right[$key] = $val;
                }
            }
            return $right;
        } elseif ($left === null && is_array($right)) {
            return $right;
        }

        return $left;
    }

    /**
     * The same as above, just with the case if both values being non-array, it will combined them to array:
     *
     * $this->mergeWithCombine(1, 2); // [1, 2]
     *
     * @param mixed $left
     * @param mixed $right
     * @return mixed[]|string
     */
    public function mergeWithCombine($left, $right)
    {
        if (is_array($left) && is_array($right)) {
            foreach ($left as $key => $val) {
                if (is_int($key)) {
                    $right[] = $val;
                } else {
                    if (isset($right[$key])) {
                        $val = $this->mergeWithCombine($val, $right[$key]);
                    }
                    $right[$key] = $val;
                }
            }
            return $right;
        } elseif ($left === null && is_array($right)) {
            return $right;
        }

        if (! empty($right) && (array) $left !== (array) $right) {
            return $this->mergeWithCombine((array) $right, (array) $left);
        }

        return $left;
    }
}
