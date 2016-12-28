<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\CodingStandard\Helper;

/**
 * Inspired by https://raw.githubusercontent.com/slevomat/coding-standard/master/SlevomatCodingStandard/Helpers/SniffSettingsHelper.php
 */
final class SniffSettingsHelper
{
    public static function normalizeArray(array $settings) : array
    {
        $settings = array_map(function ($value) {
            return trim($value);
        }, $settings);
        $settings = array_filter($settings, function ($value) {
            return $value !== '';
        });
        return array_values($settings);
    }

    public static function normalizeAssociativeArray(array $settings) : array
    {
        $normalizedSettings = [];
        foreach ($settings as $key => $value) {
            $key = trim($key);
            $value = trim($value);
            if ($key === '' || $value === '') {
                continue;
            }
            $normalizedSettings[$key] = $value;
        }

        return $normalizedSettings;
    }
}
