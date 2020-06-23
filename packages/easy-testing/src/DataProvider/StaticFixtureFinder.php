<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\DataProvider;

use Iterator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\SmartFileSystem\SmartFileInfo;

final class StaticFixtureFinder
{
    public static function yieldDirectory(string $directory, string $suffix = '*.php.inc'): Iterator
    {
        $fileInfos = self::findFilesInDirectory($directory, $suffix);

        foreach ($fileInfos as $fileInfo) {
            yield [new SmartFileInfo($fileInfo->getRealPath())];
        }
    }

    /**
     * @return SplFileInfo[]
     */
    private static function findFilesInDirectory(string $directory, string $suffix): array
    {
        $finder = Finder::create()
            ->in($directory)
            ->files()
            ->name($suffix);

        $fileInfos = iterator_to_array($finder);

        return array_values($fileInfos);
    }
}
