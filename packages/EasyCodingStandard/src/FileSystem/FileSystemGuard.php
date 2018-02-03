<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FileSystem;

use Symplify\EasyCodingStandard\Exception\FileSystem\FileNotFoundException;

final class FileSystemGuard
{
    public static function ensureFileExists(string $filename): void
    {
        if (file_exists($filename)) {
            return;
        }

        throw new FileNotFoundException(sprintf('File "%s" was not found', $filename));
    }
}
