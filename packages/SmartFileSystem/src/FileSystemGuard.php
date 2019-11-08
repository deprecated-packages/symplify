<?php declare(strict_types=1);

namespace Symplify\SmartFileSystem;

use Symplify\SmartFileSystem\Exception\DirectoryNotFoundException;
use Symplify\SmartFileSystem\Exception\FileNotFoundException;

final class FileSystemGuard
{
    public function ensureFileExists(string $file, string $location): void
    {
        if (file_exists($file)) {
            return;
        }
        throw new FileNotFoundException(sprintf('File "%s" not found in "%s".', $file, $location));
    }

    public function ensureDirectoryExists(string $directory): void
    {
        if (is_dir($directory) && file_exists($directory)) {
            return;
        }

        throw new DirectoryNotFoundException(sprintf('Directory "%s" was not found.', $directory));
    }
}
