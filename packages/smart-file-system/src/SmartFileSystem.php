<?php

declare(strict_types=1);

namespace Symplify\SmartFileSystem;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

final class SmartFileSystem extends Filesystem
{
    /**
     * @see https://github.com/symfony/filesystem/pull/4/files
     */
    public function readFile(string $filename): string
    {
        $source = @file_get_contents($filename);
        if ($source === false) {
            $message = sprintf('Failed to read "%s" because source file could not be opened for reading.', $filename);

            throw new IOException($message, 0, null, $filename);
        }

        return $source;
    }
}
