<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class PackageComposerFinder
{
    /**
     * @var string[]
     */
    private $packageDirectories = [];

    /**
     * @param string[] $packageDirectories
     */
    public function __construct(array $packageDirectories)
    {
        $this->packageDirectories = $packageDirectories;
    }

    /**
     * @return SplFileInfo[]
     */
    public function getPackageComposerFiles(): array
    {
        $iterator = Finder::create()
            ->files()
            ->in($this->packageDirectories)
            ->name('composer.json')
            ->getIterator();

        return iterator_to_array($iterator);
    }
}
