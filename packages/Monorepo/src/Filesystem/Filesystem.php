<?php declare(strict_types=1);

namespace Symplify\Monorepo\Filesystem;

use Nette\Utils\FileSystem as NetteFileSystem;
use Symfony\Component\Finder\Finder;

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
     * - find new files with finder(
     * - copy to new directory
     */
    public function copyFinderFilesToDirectory(Finder $finder, string $directory): void
    {
        foreach ($finder->getIterator() as $fileInfo) {
            NetteFileSystem::copy($fileInfo->getRealPath(), $directory . '/' . $fileInfo->getRelativePathname());
        }
    }

    public function deleteDirectory(string $directory): void
    {
        NetteFileSystem::delete($directory);
    }

    public function deleteMergedPackage(string $directory): void
    {
        $finder = Finder::create()
            ->in($directory)
            ->exclude(self::EXCLUDED_LOCAL_DIRS)
            // include .gitignore, .travis etc
            ->ignoreDotFiles(false);

        foreach ($finder->getIterator() as $fileInfo) {
            if (! file_exists($fileInfo->getPathname())) {
                continue;
            }

            NetteFileSystem::delete($fileInfo->getPathname());
        }
    }
}
