<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\Configuration;

final class ValueNormalizer
{
    public static function normalizeCommaSeparatedValues(array $values) : array
    {
        $newValues = [];
        foreach ($values as $value) {
            $newValues = array_merge($newValues, explode(',', $value));
        }

        return $newValues;
    }
}
