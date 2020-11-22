<?php

declare(strict_types=1);

namespace Symplify\StaticDetector\Strings;

use Nette\Utils\Strings;

/**
 * @see \Symplify\StaticDetector\Tests\Strings\StringsFilterTest
 */
final class StringsFilter
{
    public function isMatchOrFnMatch(string $currentValue, array $matchingValues): bool
    {
        if ($matchingValues === []) {
            return true;
        }
        foreach ($matchingValues as $matchingValue) {
            if ($matchingValue === $currentValue) {
                return true;
            }

            if (Strings::contains($matchingValue, '*') && fnmatch($matchingValue, $currentValue, FNM_NOESCAPE)) {
                return true;
            }
        }

        return false;
    }
}
