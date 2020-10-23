<?php

declare(strict_types=1);

namespace Symplify\SmartFileSystem;

use Nette\Utils\Strings;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\SmartFileSystem\Exception\DirectoryNotFoundException;
use Symplify\SmartFileSystem\Exception\FileNotFoundException;

/**
 * @see \Symplify\SmartFileSystem\Tests\SmartFileInfo\SmartFileInfoTest
 */
final class SmartFileInfo extends SplFileInfo
{
    /**
     * @var string
     * @see https://regex101.com/r/SYP00O/1
     */
    private const LAST_SUFFIX_REGEX = '#\.[^.]+$#';

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct($filePath)
    {
        $this->smartFileSystem = new SmartFileSystem();

        // accepts also dirs
        if (! file_exists($filePath)) {
            throw new FileNotFoundException(sprintf(
                'File path "%s" was not found while creating "%s" object.',
                $filePath,
                self::class
            ));
        }

        // real path doesn't work in PHAR: https://www.php.net/manual/en/function.realpath.php
        if (Strings::startsWith($filePath, 'phar://')) {
            $relativeFilePath = $filePath;
            $relativeDirectoryPath = dirname($filePath);
        } else {
            $realPath = realpath($filePath);
            $relativeFilePath = rtrim($this->smartFileSystem->makePathRelative($realPath, getcwd()), '/');
            $relativeDirectoryPath = dirname($relativeFilePath);
        }

        parent::__construct($filePath, $relativeDirectoryPath, $relativeFilePath);
    }

    public function getBasenameWithoutSuffix(): string
    {
        return pathinfo($this->getFilename())['filename'];
    }

    public function getSuffix(): string
    {
        return pathinfo($this->getFilename(), PATHINFO_EXTENSION);
    }

    public function hasSuffixes(array $suffixes): bool
    {
        return in_array($this->getSuffix(), $suffixes, true);
    }

    public function getRealPathWithoutSuffix(): string
    {
        return Strings::replace($this->getRealPath(), self::LAST_SUFFIX_REGEX, '');
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
            $this->smartFileSystem->makePathRelative($this->getNormalizedRealPath(), (string) realpath($directory)),
            '/'
        );
    }

    public function getRelativeFilePathFromCwd(): string
    {
        return $this->getRelativeFilePathFromDirectory(getcwd());
    }

    public function endsWith(string $string): bool
    {
        return Strings::endsWith($this->getNormalizedRealPath(), $string);
    }

    public function doesFnmatch(string $string): bool
    {
        if (fnmatch($string, $this->getNormalizedRealPath())) {
            return true;
        }

        // in case of relative compare
        return fnmatch('*/' . $string, $this->getNormalizedRealPath());
    }

    public function getRealPath(): string
    {
        // for phar compatibility @see https://github.com/rectorphp/rector/commit/e5d7cee69558f7e6b35d995a5ca03fa481b0407c
        return parent::getRealPath() ?: $this->getPathname();
    }

    private function getNormalizedRealPath(): string
    {
        return str_replace('\\', '/', $this->getRealPath());
    }
}
