<?php declare(strict_types=1);

namespace Symplify\Statie\Utils;

use Symplify\Statie\Exception\Utils\MissingDirectoryException;

final class FilesystemChecker
{
    public function ensureDirectoryExists(string $directory): void
    {
        if (! is_dir($directory)) {
            throw new MissingDirectoryException(
                sprintf('Directory "%s" was not found.', $directory)
            );
        }
    }
}
