<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Matcher;

final class ArrayStringAndFnMatcher
{
    public function matches(string $currenctValue, array $matchingValues): bool
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
}
