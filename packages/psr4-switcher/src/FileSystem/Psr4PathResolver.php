<?php

declare(strict_types=1);

namespace Symplify\Psr4Switcher\FileSystem;

use Symplify\Psr4Switcher\ValueObject\Psr4NamespaceToPaths;

final class Psr4PathResolver
{
    /**
     * @return string|string[]
     */
    public function resolvePaths(Psr4NamespaceToPaths $psr4NamespaceToPaths)
    {
        if (count($psr4NamespaceToPaths->getPaths()) > 1) {
            $paths = $psr4NamespaceToPaths->getPaths();
            sort($paths);
            return $paths;
        }

        return $psr4NamespaceToPaths->getPaths()[0];
    }
}
