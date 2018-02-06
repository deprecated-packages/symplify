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
    private const EXCLUDED_LOCAL_DIRS = ['packages', 'skeleton', 'vendor', '.idea', '.git', '.git-rewrite'];

    /**
     * @var string[]
     */
    private $excludedDirectories = [];

    /**
     * @return SplFileInfo[]
     */
    public function findMergedPackageFiles(string $directory): array
    {
        $finder = Finder::create()
            ->files()
            ->in($directory)
            ->exclude(self::EXCLUDED_LOCAL_DIRS)
            // include .gitignore, .travis etc
            ->ignoreDotFiles(false);

        return iterator_to_array($finder->getIterator());
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

    public function deleteMergedPackage(string $directory, string $excludeDirectory): void
    {
        $this->excludedDirectories[] = $excludeDirectory;

        $directoriesToExclude = array_merge(self::EXCLUDED_LOCAL_DIRS, $this->excludedDirectories);

        $finder = Finder::create()
            ->in($directory)
            ->exclude($directoriesToExclude)
            // include .gitignore, .travis etc
            ->ignoreDotFiles(false);

        $this->deleteFilesInFinder($finder);
    }

    private function deleteFilesInFinder(Finder $finder): void
    {
        // needs to assingned because getIterator() is lazy and it tries to find deleted directories
        /** @var SplFileInfo[] $fileInfos */
        $fileInfos = iterator_to_array($finder->getIterator());

        foreach ($fileInfos as $fileInfo) {
            if (! file_exists($fileInfo->getPathname())) {
                continue;
            }

            NetteFileSystem::delete($fileInfo->getPathname());
        }
    }
}
