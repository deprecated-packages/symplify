<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\Statie\Utils;

use DateTime;
use DateTimeInterface;
use SplFileInfo;

final class PathAnalyzer
{
    public static function startsWithDate(SplFileInfo $file) : bool
    {
        return (bool) preg_match('/(\d{4})[\/\-]*(\d{2})[\/\-]*(\d{2})[\/\-]*(\d+|)/', $file->getFilename(), $matches);
    }

    public static function detectDate(SplFileInfo $file) : DateTimeInterface
    {
        preg_match('/(\d{4})[\/\-]*(\d{2})[\/\-]*(\d{2})[\/\-]*(\d+|)/', $file->getFilename(), $matches);
        list($dummy, $year, $month, $day) = $matches;

        return new DateTime(implode('-', [$year, $month, $day]));
    }

    public static function detectFilenameWithoutDate(SplFileInfo $file) : string
    {
        preg_match(
            '/(\d{4})[\/\-]*(\d{2})[\/\-]*(\d{2})[\/\-]*(.+?)(\.[^\.]+|\.[^\.]+\.[^\.]+)$/',
            $file->getFilename(),
            $matches
        );

        return $matches[4];
    }
}
