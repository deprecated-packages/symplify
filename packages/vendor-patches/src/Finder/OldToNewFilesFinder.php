<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Finder;

use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\VendorPatches\Composer\PackageNameResolver;
use Symplify\VendorPatches\ValueObject\OldAndNewFileInfo;

final class OldToNewFilesFinder
{
    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    /**
     * @var PackageNameResolver
     */
    private $packageNameResolver;

    public function __construct(FinderSanitizer $finderSanitizer, PackageNameResolver $packageNameResolver)
    {
        $this->finderSanitizer = $finderSanitizer;
        $this->packageNameResolver = $packageNameResolver;
    }

    /**
     * @return OldAndNewFileInfo[]
     */
    public function find(string $directory): array
    {
        $oldAndNewFileInfos = [];
        $oldFileInfos = $this->findSmartFileInfosInDirectory($directory);

        foreach ($oldFileInfos as $oldFileInfo) {
            $newFilePath = rtrim($oldFileInfo->getRealPath(), '.old');
            if (! file_exists($newFilePath)) {
                continue;
            }

            $newFileInfo = new SmartFileInfo($newFilePath);
            $packageName = $this->packageNameResolver->resolveFromFileInfo($newFileInfo);

            $oldAndNewFileInfos[] = new OldAndNewFileInfo($oldFileInfo, $newFileInfo, $packageName);
        }

        return $oldAndNewFileInfos;
    }

    /**
     * @return SmartFileInfo[]
     */
    private function findSmartFileInfosInDirectory(string $directory): array
    {
        $finder = Finder::create()
            ->in($directory)
            ->files()
            // excluded built files
            ->exclude('composer/')
            ->exclude('ocramius/')
            ->name('*.php.old');

        return $this->finderSanitizer->sanitize($finder);
    }
}
