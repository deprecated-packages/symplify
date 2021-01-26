<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\DataProvider;

use Iterator;
use Nette\Utils\Strings;
use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

/**
 * @see \Symplify\EasyTesting\Tests\DataProvider\StaticFixtureFinder\StaticFixtureFinderTest
 */
final class StaticFixtureFinder
{
    public static function yieldDirectory(string $directory, string $suffix = '*.php.inc'): Iterator
    {
        $finder = self::findFilesInDirectory($directory, $suffix);
        foreach ($finder as $fileInfo) {
            yield [new SmartFileInfo($fileInfo->getRealPath())];
        }
    }

    public static function yieldDirectoryExclusively(string $directory, string $suffix = '*.php.inc'): Iterator
    {
        $finder = self::findFilesInDirectoryExclusively($directory, $suffix);
        foreach ($finder as $fileInfo) {
            yield [new SmartFileInfo($fileInfo->getRealPath())];
        }
    }

    public static function yieldDirectoryByFileName(string $directory, string $suffix = '*.php.inc'): Iterator
    {
        $finder = self::findFilesInDirectory($directory, $suffix);
        foreach ($finder as $fileInfo) {
            yield $fileInfo->getRelativePathname() => [new SmartFileInfo($fileInfo->getRealPath())];
        }
    }

    public static function yieldDirectoryExclusivelyByFileName(string $directory, string $suffix = '*.php.inc'): Iterator
    {
        $finder = self::findFilesInDirectoryExclusively($directory, $suffix);
        foreach ($finder as $fileInfo) {
            yield $fileInfo->getRelativePathname() => [new SmartFileInfo($fileInfo->getRealPath())];
        }
    }

    private static function findFilesInDirectory(string $directory, string $suffix): Finder
    {
        return Finder::create()
            ->in($directory)
            ->files()
            ->name($suffix);
    }

    private static function findFilesInDirectoryExclusively(string $directory, string $suffix): Finder
    {
        self::ensureNoOtherFileName($directory, $suffix);

        return Finder::create()->in($directory)
            ->files()
            ->name($suffix);
    }

    private static function ensureNoOtherFileName(string $directory, string $suffix): void
    {
        $finder = Finder::create()->in($directory)
            ->files()
            ->notName($suffix);

        $relativeFilePaths = [];
        foreach ($finder as $fileInfo) {
            $relativeFilePaths[] = Strings::substring($fileInfo->getRealPath(), strlen(getcwd()) + 1);
        }

        if ($relativeFilePaths === []) {
            return;
        }

        throw new ShouldNotHappenException(sprintf(
            'Files "%s" have invalid suffix, use "%s" suffix instead',
            implode('", ', $relativeFilePaths),
            $suffix
        ));
    }
}
