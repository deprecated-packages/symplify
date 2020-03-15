<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\FileSystem;

use Nette\Utils\FileSystem;
use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymfonyStaticDumper\Configuration\SymfonyStaticDumperConfiguration;

final class AssetsCopier
{
    /**
     * @var SymfonyStaticDumperConfiguration
     */
    private $symfonyStaticDumperConfiguration;

    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    public function __construct(
        SymfonyStaticDumperConfiguration $symfonyStaticDumperConfiguration,
        FinderSanitizer $finderSanitizer
    ) {
        $this->symfonyStaticDumperConfiguration = $symfonyStaticDumperConfiguration;
        $this->finderSanitizer = $finderSanitizer;
    }

    public function copyAssets(): void
    {
        $assetFileInfos = $this->findAssetFileInfos();

        foreach ($assetFileInfos as $assetFileInfo) {
            $relativePathFromRoot = $assetFileInfo->getRelativeFilePathFromDirectory(
                $this->symfonyStaticDumperConfiguration->getPublicDirectory()
            );

            FileSystem::copy(
                $assetFileInfo->getRealPath(),
                $this->symfonyStaticDumperConfiguration->getOutputDirectory() . '/' . $relativePathFromRoot
            );
        }
    }

    /**
     * @return SmartFileInfo[]
     */
    private function findAssetFileInfos(): array
    {
        $finder = new Finder();
        $finder->files()
            ->in($this->symfonyStaticDumperConfiguration->getPublicDirectory())
            ->notName('*.php');

        return $this->finderSanitizer->sanitize($finder);
    }
}
