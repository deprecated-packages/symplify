<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\FileSystem;

use Nette\Utils\FileSystem;
use Symplify\PackageBuilder\Exception\Configuration\FileNotFoundException;
use Symplify\PackageBuilder\Exception\FilePathNotAbsoluteException;

final class FileGuard
{
    public function ensureFileExists(string $file, string $location): void
    {
        if (file_exists($file)) {
            return;
        }

        throw new FileNotFoundException(sprintf('File "%s" not found in "%s".', $file, $location));
    }

    public function ensureIsAbsolutePath(string $file, string $location): void
    {
        if (FileSystem::isAbsolute($file)) {
            return;
        }

        throw new FilePathNotAbsoluteException(sprintf(
            'File path "%s" is not absolute in "%s".',
            $file,
            $location
        ));
    }
}
