<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Utils;

final class PathNormalizer
{
    public static function normalize(string $path) : string
    {
        return strtr($path, '\\', DIRECTORY_SEPARATOR);
    }
}
