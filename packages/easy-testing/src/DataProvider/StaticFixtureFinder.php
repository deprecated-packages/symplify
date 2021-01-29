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
    /**
     * @var bool
     */
    public static $enableValidation = true;

    public static function yieldDirectory(string $directory, string $suffix = '*.php.inc'): Iterator
    {
        $fileInfos = self::findFilesInDirectory($directory, $suffix);

        foreach ($fileInfos as $fileInfo) {
            yield [new SmartFileInfo($fileInfo->getRealPath())];
        }

        static::$enableValidation = true;
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

        $fileInfos = iterator_to_array($finderSuffix);

        if (self::$enableValidation) {
            self::validateFixtureSuffix($finderAll, $finderSuffix, $fileInfos, $suffix);
        }

        return array_values($fileInfos);
    }

    private static function validateFixtureSuffix(
        Finder $finderAll,
        Finder $finderSuffix,
        array $fileInfos,
        string $suffix
    ): void {
        if (count($finderSuffix) === count($finderAll)) {
            return;
        }

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
    }
}
