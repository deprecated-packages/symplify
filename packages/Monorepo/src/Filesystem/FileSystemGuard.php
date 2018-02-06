<?php declare(strict_types=1);

namespace Symplify\Monorepo\Filesystem;

use Symplify\Monorepo\Exception\Filesystem\DirectoryNotFoundException;

final class FileSystemGuard
{
    public function ensureDirectoryExists(string $directory): void
    {
        if (is_dir($directory) && file_exists($directory)) {
            return;
        }

        throw new DirectoryNotFoundException(
            sprintf('Directory "%s" was not found.', $directory)
        );
    }
}
