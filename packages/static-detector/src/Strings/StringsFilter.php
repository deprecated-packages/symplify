<?php

declare(strict_types=1);

namespace Symplify\StaticDetector\Strings;

/**
 * @see \Symplify\StaticDetector\Tests\Strings\StringsFilterTest
 */
final class StringsFilter
{
    /**
     * @param string[] $matchingValues
     */
    public function isMatchOrFnMatch(string $currentValue, array $matchingValues): bool
    {
        if ($matchingValues === []) {
            return true;
        }
        foreach ($matchingValues as $matchingValue) {
            if ($matchingValue === $currentValue) {
                return true;
            }

            if (fnmatch($matchingValue, $currentValue, FNM_NOESCAPE)) {
                return true;
            }
        }

        return false;
    }
}
