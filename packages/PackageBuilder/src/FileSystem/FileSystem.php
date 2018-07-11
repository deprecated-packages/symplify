<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\FileSystem;

final class FileSystem
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
