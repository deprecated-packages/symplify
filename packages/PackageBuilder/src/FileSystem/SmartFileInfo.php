<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\FileSystem;

use Nette\Utils\Strings;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\PackageBuilder\Exception\FileSystem\DirectoryNotFoundException;
use Symplify\PackageBuilder\Exception\FileSystem\FileNotFoundException;

final class SmartFileInfo extends SplFileInfo
{
    /**
     * @param mixed $filePath
     */
    public function __construct($filePath)
    {
        $realPath = realpath($filePath);

        if (! file_exists($filePath) || $realPath === false) {
            throw new FileNotFoundException(sprintf(
                'File path "%s" was not found while creating "%s" object.',
                $filePath,
                self::class
            ));
        }

        $relativeFilePath = Strings::substring($realPath, strlen(getcwd()) + 1);
        $relativeDirectoryPath = dirname($relativeFilePath);

        parent::__construct($filePath, $relativeDirectoryPath, $relativeFilePath);
    }

    public function getBasenameWithoutSuffix(): string
    {
        return pathinfo($this->getFilename())['filename'];
    }

    public function getRelativeFilePath(): string
    {
        return $this->getRelativePathname();
    }

    public function getRelativeDirectoryPath(): string
    {
        return $this->getRelativePath();
    }

    public function getRelativeFilePathFromDirectory(string $directory): string
    {
        if (! file_exists($directory)) {
            throw new DirectoryNotFoundException(sprintf(
                'Directory "%s" was not found in %s.',
                $directory,
                self::class
            ));
        }

        return Strings::substring($this->getRealPath(), Strings::length(realpath($directory)) + 1);
    }

    public function endsWith(string $string): bool
    {
        return Strings::endsWith($this->getNormalizedRealPath(), $string);
    }

    public function fnmatches(string $string): bool
    {
        if (fnmatch($string, $this->getNormalizedRealPath())) {
            return true;
        }

        // in case of relative compare
        return fnmatch('*/' . $string, $this->getNormalizedRealPath());
    }

    private function getNormalizedRealPath(): string
    {
        return str_replace('\\', '/', $this->getRealPath());
    }
}
