<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\DataProvider;

use Iterator;
use Nette\Utils\Strings;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

/**
 * @see \Symplify\EasyTesting\Tests\DataProvider\StaticFixtureFinder\StaticFixtureFinderTest
 */
final class StaticFixtureFinder
{
    public static function yieldDirectory(string $directory, string $suffix = '*.php.inc'): Iterator
    {
        $fileInfos = self::findFilesInDirectory($directory, $suffix);
        foreach ($fileInfos as $fileInfo) {
            yield [new SmartFileInfo($fileInfo->getRealPath())];
        }
    }

    public static function yieldDirectoryExclusively(string $directory, string $suffix = '*.php.inc'): Iterator
    {
        $fileInfos = self::findFilesInDirectoryExclusively($directory, $suffix);
        foreach ($fileInfos as $fileInfo) {
            yield [new SmartFileInfo($fileInfo->getRealPath())];
        }
    }

    /**
     * @return SplFileInfo[]
     */
    private static function findFilesInDirectory(string $directory, string $suffix): array
    {
        $finder = Finder::create()->in($directory)->files()->name($suffix);
        $fileInfos = iterator_to_array($finder);

        return array_values($fileInfos);
    }

    /**
     * @return SplFileInfo[]
     */
    private static function findFilesInDirectoryExclusively(string $directory, string $suffix): array
    {
        self::ensureNoOtherFileName($directory, $suffix);

        $finder = Finder::create()->in($directory)
            ->files()
            ->name($suffix);

        $fileInfos = iterator_to_array($finder->getIterator());
        return array_values($fileInfos);
    }

    private static function ensureNoOtherFileName(string $directory, string $suffix): void
    {
        $iterator = Finder::create()->in($directory)
            ->files()
            ->notName($suffix)
            ->getIterator();

        $relativeFilePaths = [];
        foreach ($iterator as $fileInfo) {
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
