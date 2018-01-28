<?php declare(strict_types=1);

namespace Symplify\Monorepo\Filesystem;

use Nette\Utils\FileSystem as NetteFileSystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class Filesystem
{
    /**
     * @var string[]
     */
    private const EXCLUDED_LOCAL_DIRS = ['packages', 'vendor', '.idea'];

    public function findMergedPackageFiles(string $directory): Finder
    {
        return Finder::create()
            ->files()
            ->in($directory)
            ->exclude(self::EXCLUDED_LOCAL_DIRS)
            // include .gitignore, .travis etc
            ->ignoreDotFiles(false);
    }

    /**
     * - find new files with finder
     * - copy to new directory
     */
    public function copyFinderFilesToDirectory(Finder $finder, string $directory): void
    {
        foreach ($finder->getIterator() as $fileInfo) {
            NetteFileSystem::copy($fileInfo->getRealPath(), $directory . '/' . $fileInfo->getRelativePathname());
        }
    }

    public function clearEmptyDirectories(string $path): void
    {
        $emptyDirectoriesFinder = Finder::create()
            ->directories()
            ->in($path)
            ->exclude(self::EXCLUDED_LOCAL_DIRS)
            // sort from deepest to top to allow removal in same direction
            ->sort(function (SplFileInfo $firstFileInfo, SplFileInfo $secondFileInfo) {
                return strlen($firstFileInfo->getRealPath()) < strlen($secondFileInfo->getRealPath());
            })
            // empty directory
            ->size(0);

        foreach ($emptyDirectoriesFinder->getIterator() as $emptyDirectoryFileInfo) {
            rmdir($emptyDirectoryFileInfo->getRelativePathname());
        }
    }
}
