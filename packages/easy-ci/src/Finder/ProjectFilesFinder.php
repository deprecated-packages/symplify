<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Finder;

use Symplify\SmartFileSystem\Finder\SmartFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ProjectFilesFinder
{
    public function __construct(
        private SmartFinder $smartFinder
    ) {
    }

    /**
     * @return SmartFileInfo[]
     */
    public function find(array $sources): array
    {
        $paths = [];
        foreach ($sources as $source) {
            $paths[] = getcwd() . DIRECTORY_SEPARATOR . $source;
        }

        return $this->smartFinder->find($paths, '*');
    }
}
