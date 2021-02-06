<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Finder;

use Symplify\SmartFileSystem\Finder\SmartFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ProjectFilesFinder
{
    /**
     * @var SmartFinder
     */
    private $smartFinder;

    public function __construct(SmartFinder $smartFinder)
    {
        $this->smartFinder = $smartFinder;
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
