<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Matcher;

use Nette\Utils\Arrays;

/**
 * @see https://gist.github.com/kyledseever/3014950
 */
final class SharedNamePrefixMatcher
{
    /**
     * @param string[] $values
     * @return array<string, string[]>
     */
    public function match(array $values): array
    {
        $groups = [];

        $valuesCount = \count($values);

        for ($i = 0; $i < $valuesCount; ++$i) {
            for ($j = $i + 1; $j < $valuesCount; ++$j) {
                $pos = $this->strcmppos($values[$i], $values[$j]);
                $prefix = \substr($values[$i], 0, $pos + 1);

                // append to grouping for this prefix. include both strings - this
                // gives duplicates which we'll merge later
                $groups[$prefix][] = [$values[$i], $values[$j]];
            }
        }

        $uniqueGroupsByPrefix = [];
        foreach ($groups as $prefix => $group) {
            // to remove duplicates introduced above
            $uniqueGroups = \array_unique(Arrays::flatten($group));

            $uniqueGroupsByPrefix[$prefix] = $uniqueGroups;
        }

        return $uniqueGroupsByPrefix;
    }

    private function strcmppos(string $left, string $right): int
    {
        if ($left === '') {
            return -1;
        }

        if ($right === '') {
            return -1;
        }

        $position = 0;
        while ($left[$position] && $left[$position] === $right[$position]) {
            ++$position;
        }

        return $position - 1;
    }
}
