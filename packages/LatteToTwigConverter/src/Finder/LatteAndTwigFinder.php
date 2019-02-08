<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\Finder;

use Symfony\Component\Finder\Finder;
use Symplify\PackageBuilder\FileSystem\FinderSanitizer;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class LatteAndTwigFinder
{
    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    public function __construct(FinderSanitizer $finderSanitizer)
    {
        $this->finderSanitizer = $finderSanitizer;
    }

    /**
     * @return SmartFileInfo[]
     */
    public function findTwigFilesInSource(string $source): array
    {
        if (is_file($source) && file_exists($source)) {
            return [new SmartFileInfo($source)];
        }

        return $this->findFilesInDirectoryBySuffix($source, 'twig');
    }

    /**
     * @return SmartFileInfo[]
     */
    public function findLatteFilesInSource(string $source): array
    {
        if (is_file($source) && file_exists($source)) {
            return [new SmartFileInfo($source)];
        }

        return $this->findFilesInDirectoryBySuffix($source, 'latte');
    }

    /**
     * @return SmartFileInfo[]
     */
    private function findFilesInDirectoryBySuffix(string $sourceDirectory, string $suffix): array
    {
        $twigFileFinder = Finder::create()
            ->files()
            ->in($sourceDirectory)
            ->name('#\.' . $suffix . '$#')
            ->sortByName();

        return $this->finderSanitizer->sanitize($twigFileFinder);
    }
}
