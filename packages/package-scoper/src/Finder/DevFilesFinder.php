<?php

declare(strict_types=1);

namespace Symplify\PackageScoper\Finder;

use Symplify\SmartFileSystem\Finder\SmartFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

final class DevFilesFinder
{
    /**
     * @var SmartFinder
     */
    private $smartFinder;

    public function __construct(SmartFinder $smartFinder)
    {
        $this->smartFinder = $smartFinder;
    }

    /**
     * @param string[] $source
     * @return string[]
     */
    public function findDevFilesPaths(array $source): array
    {
        $filePaths = [];

        $suffixFileInfos = $this->smartFinder->find($source, '#.(\.php\.inc|Test\.php)$#');
        $testFileInfos = $this->smartFinder->findPaths($source, '#.\/tests\/.#');

        /** @var SmartFileInfo[] $fileInfos */
        $fileInfos = array_merge($suffixFileInfos, $testFileInfos);

        foreach ($fileInfos as $fileInfo) {
            $filePaths[] = $fileInfo->getRelativeFilePathFromCwd();
        }

        return $filePaths;
    }
}
