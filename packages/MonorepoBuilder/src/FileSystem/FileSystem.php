<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\FileSystem;

final class FileSystem
{
    public function normalizeSlashes(string $path): string
    {
        return str_replace(['/', '\\'], '/', $path);
    }
}
