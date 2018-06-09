<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class PackageComposerFinder
{
    /**
     * @return SplFileInfo[]
     */
    public function getPackageComposerFiles(): array
    {
        $iterator = Finder::create()
            ->files()
            ->in(getcwd() . '/packages')
            ->name('composer.json')
            ->getIterator();

        return iterator_to_array($iterator);
    }
}
