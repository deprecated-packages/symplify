<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

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
