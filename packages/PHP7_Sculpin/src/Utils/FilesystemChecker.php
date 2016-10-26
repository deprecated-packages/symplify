<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Utils;

use Symplify\PHP7_Sculpin\Exception\Utils\MissingDirectoryException;

final class FilesystemChecker
{
    public static function ensureDirectoryExists(string $directory)
    {
        if (! is_dir($directory)) {
            throw new MissingDirectoryException(
                sprintf('Directory "%s" was not found.', $directory)
            );
        }
    }
}
