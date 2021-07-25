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
    public function __construct(
        private PackageNamesProvider $packageNamesProvider,
        private JsonFileManager $jsonFileManager,
        private SymfonyStyle $symfonyStyle,
        private ComposerJsonSymlinker $composerJsonSymlinker,
        private UsedPackagesResolver $usedPackagesResolver,
        private ConsoleDiffer $consoleDiffer
    ) {
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

        $decoreatedPackageComposerJson = $this->composerJsonSymlinker->decoratePackageComposerJsonWithPackageSymlinks(
            $packageFileInfo,
            $packageNames,
            $rootComposerJsonFileInfo,
            $symlink
        );

        $newComposerJsonContents = $this->jsonFileManager->printJsonToFileInfo(
            $decoreatedPackageComposerJson,
            $packageFileInfo
        );

        $message = sprintf('File "%s" was updated', $packageFileInfo->getRelativeFilePathFromCwd());
        $this->symfonyStyle->title($message);

        $diff = $this->consoleDiffer->diff($oldComposerJsonContents, $newComposerJsonContents);
        $this->symfonyStyle->writeln($diff);

        $this->symfonyStyle->newLine(2);
    }
}
