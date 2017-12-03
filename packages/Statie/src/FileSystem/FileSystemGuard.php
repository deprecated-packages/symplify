<?php declare(strict_types=1);

namespace Symplify\Statie\FileSystem;

use Symplify\Statie\Exception\Utils\MissingDirectoryException;

final class FileSystemGuard
{
    public function ensureDirectoryExists(string $directory): void
    {
        if (is_dir($directory)) {
            return;
        }

        throw new MissingDirectoryException(
            sprintf('Directory "%s" was not found.', $directory)
        );
    }
}
