<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\FileSystem;

use Symplify\PackageBuilder\Exception\FileSystem\DirectoryNotFoundException;
use function Safe\sprintf;

final class FileSystemGuard
{
    public function ensureDirectoryExists(string $directory): void
    {
        if (is_dir($directory) && file_exists($directory)) {
            return;
        }

        throw new DirectoryNotFoundException(sprintf('Directory "%s" was not found.', $directory));
    }
}
