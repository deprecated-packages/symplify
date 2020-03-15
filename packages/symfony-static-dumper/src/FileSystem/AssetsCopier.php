<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\FileSystem;

use Nette\Utils\FileSystem;
use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;

final class AssetsCopier
{
    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    public function __construct(FinderSanitizer $finderSanitizer)
    {
        $this->finderSanitizer = $finderSanitizer;
    }

    public function copyAssets(string $publicDirectory, string $outputDirectory): void
    {
        $assetFileInfos = $this->findAssetFileInfos($publicDirectory);

        foreach ($assetFileInfos as $assetFileInfo) {
            $relativePathFromRoot = $assetFileInfo->getRelativeFilePathFromDirectory($publicDirectory);

            FileSystem::copy($assetFileInfo->getRealPath(), $outputDirectory . '/' . $relativePathFromRoot);
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
