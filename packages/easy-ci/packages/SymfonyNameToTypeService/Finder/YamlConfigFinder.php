<?php

declare(strict_types=1);

namespace Symplify\EasyCI\SymfonyNameToTypeService\Finder;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @see \Symplify\EasyCI\Tests\SymfonyNameToTypeService\Finder\YamlConfigFinder\YamlConfigFinderTest
 */
final class YamlConfigFinder
{
    /**
     * @return SplFileInfo[]
     */
    public function findInDirectory(string $directory): array
    {
        $finder = new Finder();
        $yamlFiles = $finder->files()
            ->in($directory)
            ->name('*.yml')
            ->name('*.yaml');

        return iterator_to_array($yamlFiles);
    }
}
