<?php

declare(strict_types=1);

namespace Symplify\Psr4Switcher\Finder;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\Psr4Switcher\RobotLoader\PhpClassLoader;

final class MultipleClassInOneFileFinder
{
    public function __construct(
        private PhpClassLoader $phpClassLoader,
        private SymfonyStyle $symfonyStyle,
    ) {
    }

    /**
     * @param string[] $directories
     * @return string[][]
     */
    public function findInDirectories(array $directories): array
    {
        $fileByClasses = $this->phpClassLoader->load($directories);

        $message = sprintf('Analyzing %d PHP files', count($fileByClasses));
        $this->symfonyStyle->note($message);

        $classesByFile = [];
        foreach ($fileByClasses as $class => $file) {
            $classesByFile[$file][] = $class;
        }

        return array_filter($classesByFile, fn (array $classes): bool => count($classes) >= 2);
    }
}
