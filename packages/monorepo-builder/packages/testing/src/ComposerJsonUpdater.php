<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Testing;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\Package\PackageNamesProvider;
use Symplify\MonorepoBuilder\Testing\ComposerJson\ComposerJsonSymlinker;
use Symplify\MonorepoBuilder\Testing\ComposerJson\ComposerVersionManipulator;
use Symplify\MonorepoBuilder\Testing\PackageDependency\UsedPackagesResolver;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ComposerJsonUpdater
{
    /**
     * @var PackageNamesProvider
     */
    private $packageNamesProvider;

    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var ComposerJsonSymlinker
     */
    private $composerJsonSymlinker;

    /**
     * @var ComposerVersionManipulator
     */
    private $composerVersionManipulator;

    /**
     * @var UsedPackagesResolver
     */
    private $usedPackagesResolver;

    public function __construct(
        PackageNamesProvider $packageNamesProvider,
        JsonFileManager $jsonFileManager,
        SymfonyStyle $symfonyStyle,
        ComposerJsonSymlinker $composerJsonSymlinker,
        ComposerVersionManipulator $composerVersionManipulator,
        UsedPackagesResolver $usedPackagesResolver
    ) {
        $this->packageNamesProvider = $packageNamesProvider;
        $this->jsonFileManager = $jsonFileManager;
        $this->symfonyStyle = $symfonyStyle;
        $this->composerJsonSymlinker = $composerJsonSymlinker;
        $this->composerVersionManipulator = $composerVersionManipulator;
        $this->usedPackagesResolver = $usedPackagesResolver;
    }

    public function processPackage(SmartFileInfo $packageFileInfo, SmartFileInfo $mainComposerJsonFileInfo): void
    {
        $packageComposerJson = $this->jsonFileManager->loadFromFileInfo($packageFileInfo);

        $usedPackageNames = $this->usedPackagesResolver->resolveForPackage($packageComposerJson);
        $packageComposerJson = $this->composerVersionManipulator->setAsteriskVersionForUsedPackages(
            $packageComposerJson,
            $usedPackageNames
        );

        // possibly replace them all to cover recursive secondary dependencies
        $packageNames = $this->packageNamesProvider->provide();

        $packageComposerJson = $this->composerJsonSymlinker->decoratePackageComposerJsonWithPackageSymlinks(
            $packageComposerJson,
            $packageNames,
            $mainComposerJsonFileInfo
        );

        $message = sprintf('File "%s" was updated', $packageFileInfo->getRelativeFilePathFromCwd());
        $this->symfonyStyle->note($message);

        $this->jsonFileManager->saveJsonWithFileInfo($packageComposerJson, $packageFileInfo);
    }
}
