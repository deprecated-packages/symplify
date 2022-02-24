<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\Finder;

use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;

final class FixtureFinder
{
    public function __construct(
        private FinderSanitizer $finderSanitizer
    ) {
    }

    /**
     * @param string[] $sources
     * @return SmartFileInfo[]
     */
    public function find(array $sources): array
    {
        $finder = new Finder();
        $finder->files()
            ->in($sources)
            ->name('*.php.inc')
            ->path('Fixture')
            ->sortByName();

        return $this->finderSanitizer->sanitize($finder);
    }
}
