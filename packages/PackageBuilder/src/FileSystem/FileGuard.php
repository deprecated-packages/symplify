<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\FileSystem;

use Symplify\PackageBuilder\Exception\Configuration\FileNotFoundException;
use function Safe\sprintf;

final class FileGuard
{
    public function ensureFileExists(string $file, string $location): void
    {
        if (file_exists($file)) {
            return;
        }

        throw new FileNotFoundException(sprintf('File "%s" not found in "%s".', $file, $location));
    }
}
