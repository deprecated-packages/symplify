<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Matcher;

final class ArrayStringAndFnMatcher
{
    /**
     * @param string[] $matchingValues
     */
    public function isMatch(string $currenctValue, array $matchingValues): bool
    {
        foreach ($matchingValues as $matchingValue) {
            if ($currenctValue === $matchingValue) {
                return true;
            }

            if (fnmatch($matchingValue, $currenctValue)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string[] $matchingValues
     */
    public function isMatchOrSubType(string $currenctValue, array $matchingValues): bool
    {
        if ($this->isMatch($currenctValue, $matchingValues)) {
            return true;
        }

        foreach ($matchingValues as $matchingValue) {
            if (is_a($currenctValue, $matchingValue, true)) {
                return true;
            }
        }

        return false;
    }
}
