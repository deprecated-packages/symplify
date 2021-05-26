<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Testing;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\ConsoleColorDiff\Console\Output\ConsoleDiffer;
use Symplify\MonorepoBuilder\Package\PackageNamesProvider;
use Symplify\MonorepoBuilder\Testing\ComposerJson\ComposerJsonSymlinker;
use Symplify\MonorepoBuilder\Testing\PackageDependency\UsedPackagesResolver;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class ComposerJsonRepositoriesUpdater
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
     * @var UsedPackagesResolver
     */
    private $usedPackagesResolver;

    /**
     * @var ConsoleDiffer
     */
    private $consoleDiffer;

    public function __construct(
        PackageNamesProvider $packageNamesProvider,
        JsonFileManager $jsonFileManager,
        SymfonyStyle $symfonyStyle,
        ComposerJsonSymlinker $composerJsonSymlinker,
        UsedPackagesResolver $usedPackagesResolver,
        ConsoleDiffer $consoleDiffer
    ) {
        $this->packageNamesProvider = $packageNamesProvider;
        $this->jsonFileManager = $jsonFileManager;
        $this->symfonyStyle = $symfonyStyle;
        $this->composerJsonSymlinker = $composerJsonSymlinker;
        $this->usedPackagesResolver = $usedPackagesResolver;
        $this->consoleDiffer = $consoleDiffer;
    }

    public function processPackage(SmartFileInfo $packageFileInfo, ComposerJson $rootComposerJson, bool $symlink): void
    {
        $packageComposerJson = $this->jsonFileManager->loadFromFileInfo($packageFileInfo);

        $usedPackageNames = $this->usedPackagesResolver->resolveForPackage($packageComposerJson);
        if ($usedPackageNames === []) {
            $message = sprintf(
                'Package "%s" does not use any mutual dependencies, so we skip it',
                $packageFileInfo->getRelativeFilePathFromCwd()
            );
            $this->symfonyStyle->note($message);
            return;
        }

        // possibly replace them all to cover recursive secondary dependencies
        $packageNames = $this->packageNamesProvider->provide();

        $oldComposerJsonContents = $packageFileInfo->getContents();

        $rootComposerJsonFileInfo = $rootComposerJson->getFileInfo();
        if (! $rootComposerJsonFileInfo instanceof SmartFileInfo) {
            throw new ShouldNotHappenException();
        }

        $packageComposerJson = $this->composerJsonSymlinker->decoratePackageComposerJsonWithPackageSymlinks(
            $packageFileInfo,
            $packageNames,
            $rootComposerJsonFileInfo,
            $symlink
        );

        $newComposerJsonContents = $this->jsonFileManager->printJsonToFileInfo($packageComposerJson, $packageFileInfo);

        $message = sprintf('File "%s" was updated', $packageFileInfo->getRelativeFilePathFromCwd());
        $this->symfonyStyle->title($message);

        $diff = $this->consoleDiffer->diff($oldComposerJsonContents, $newComposerJsonContents);
        $this->symfonyStyle->writeln($diff);

        $this->symfonyStyle->newLine(2);
    }
}
