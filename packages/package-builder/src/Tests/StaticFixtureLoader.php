<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests;

use Iterator;
use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\SmartFileInfo;

final class StaticFixtureLoader
{
    /**
     * @return Iterator<SmartFileInfo[]>
     */
    public static function loadFromDirectory(string $directory): Iterator
    {
        $finder = (new Finder())->files()
            ->in($directory);

        foreach ($finder as $fileInfo) {
            yield [new SmartFileInfo($fileInfo->getRealPath())];
        }
    }
}
