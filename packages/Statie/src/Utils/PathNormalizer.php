<?php declare(strict_types=1);

namespace Symplify\Statie\Utils;

final class PathNormalizer
{
    public function normalize(string $path): string
    {
        return str_replace(['\\', '/'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $path);
    }
}
