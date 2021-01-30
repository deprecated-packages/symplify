<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\DataProvider;

use Iterator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

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
        $finderAll = Finder::create()->in($directory)->files();

        foreach ($finderAll as $key => $fileInfoAll) {
            $fileNameFromAll = $fileInfoAll->getFileName();
            if (! isset($fileInfos[$key])) {
                throw new ShouldNotHappenException(sprintf(
                    '"%s" has invalid suffix, use "%s" suffix instead',
                    $fileNameFromAll,
                    $suffix
                ));
            }
        }

        return array_values($fileInfos);
    }

    /**
     * @return SplFileInfo[]
     */
    private static function findFilesInDirectoryExclusively(string $directory, string $suffix): array
    {
        $finder = Finder::create()->in($directory)->files()->name($suffix);
        $fileInfos = iterator_to_array($finder);

        return array_values($fileInfos);
    }
}
