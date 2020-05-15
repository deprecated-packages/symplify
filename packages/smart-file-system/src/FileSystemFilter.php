<?php

declare(strict_types=1);

namespace Symplify\SmartFileSystem;

/**
 * @see \Symplify\SmartFileSystem\Tests\FileSystemFilter\FileSystemFilterTest
 */
final class FileSystemFilter
{
    /**
     * @param mixed[] $filesAndDirectories
     * @return mixed[][]
     */
    public function separateFilesAndDirectories(array $filesAndDirectories): array
    {
        $files = [];
        $directories = [];

        foreach ($filesAndDirectories as $filesOrDirectory) {
            if (is_file($filesOrDirectory)) {
                $files[] = $filesOrDirectory;
            } else {
                $directories[] = $filesOrDirectory;
            }
        }

        return [$files, $directories];
    }
}
