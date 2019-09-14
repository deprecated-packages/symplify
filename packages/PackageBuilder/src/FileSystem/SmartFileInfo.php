<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\FileSystem;

use Nette\Utils\Strings;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\PackageBuilder\Exception\FileSystem\DirectoryNotFoundException;
use Symplify\PackageBuilder\Exception\FileSystem\FileNotFoundException;

final class SmartFileInfo extends SplFileInfo
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param mixed $filePath
     */
    public function __construct($filePath)
    {
        $this->filesystem = new Filesystem();

        $realPath = realpath($filePath);
        if (! file_exists($filePath) || $realPath === false) {
            throw new FileNotFoundException(sprintf(
                'File path "%s" was not found while creating "%s" object.',
                $filePath,
                self::class
            ));
        }

        $relativeFilePath = rtrim($this->filesystem->makePathRelative($realPath, getcwd()), '/');
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

        return rtrim(
            $this->filesystem->makePathRelative($this->getNormalizedRealPath(), (string) realpath($directory)),
            '/'
        );
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
