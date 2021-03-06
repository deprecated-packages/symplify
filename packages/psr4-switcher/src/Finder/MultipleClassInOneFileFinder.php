<?php

declare(strict_types=1);

namespace Symplify\Psr4Switcher\Finder;

use Symplify\Psr4Switcher\RobotLoader\PhpClassLoader;

final class MultipleClassInOneFileFinder
{
    /**
     * @var PhpClassLoader
     */
    private $phpClassLoader;

    public function __construct(PhpClassLoader $phpClassLoader)
    {
        $this->phpClassLoader = $phpClassLoader;
    }

    /**
     * @param string[] $directories
     * @return string[][]
     */
    public function findInDirectories(array $directories): array
    {
        $fileByClasses = $this->phpClassLoader->load($directories);

        $classesByFile = [];
        foreach ($fileByClasses as $class => $file) {
            $classesByFile[$file][] = $class;
        }

        return array_filter($classesByFile, function ($classes): bool {
            return count($classes) >= 2;
        });
    }
}
