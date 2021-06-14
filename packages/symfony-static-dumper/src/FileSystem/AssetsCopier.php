<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\FileSystem;

use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

/**
 * @see \Symplify\SymfonyStaticDumper\Tests\FileSystem\AssetsCopierTest
 */
final class AssetsCopier
{
    public function __construct(
        private FinderSanitizer $finderSanitizer,
        private SmartFileSystem $smartFileSystem
    ) {
    }

    public function copyAssets(string $publicDirectory, string $outputDirectory): void
    {
        $assetFileInfos = $this->findAssetFileInfos($publicDirectory);

        foreach ($assetFileInfos as $assetFileInfo) {
            $relativePathFromRoot = $assetFileInfo->getRelativeFilePathFromDirectory($publicDirectory);

            $this->smartFileSystem->copy($assetFileInfo->getRealPath(), $outputDirectory . '/' . $relativePathFromRoot);
        }
    }

    /**
     * @return SmartFileInfo[]
     */
    private function findAssetFileInfos(string $publicDirectory): array
    {
        $finder = new Finder();
        $finder->files()
            ->in($publicDirectory)
            ->notName('*.php');

        return $this->finderSanitizer->sanitize($finder);
    }
}
