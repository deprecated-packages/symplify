<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\DataProvider;

use Nette\Utils\Strings;
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

    /**
     * @return SplFileInfo[]
     */
    private static function findFilesInDirectory(string $directory, string $suffix): array
    {
        $finderSuffix = Finder::create()
            ->in($directory)
            ->files()
            ->name($suffix);

        $finderAll = Finder::create()
            ->in($directory)
            ->files();

        if (count($finderSuffix) !== count($finderAll)) {
            foreach ($finderAll as $fileInfo) {
                $fileName = $fileInfo->getFileName();
                if (! Strings::endsWith($fileName, $suffix)) {
                    throw new ShouldNotHappenException(sprintf(
                        '"%s" has invalid suffix, use "%s" suffix instead',
                        $fileName,
                        $suffix
                    ));
                }
            }
        }

        $fileInfos = iterator_to_array($finderSuffix);
        return array_values($fileInfos);
    }
}
