<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Testing;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\ConsoleColorDiff\Console\Output\ConsoleDiffer;
use Symplify\MonorepoBuilder\Testing\ComposerJson\ComposerVersionManipulator;
use Symplify\MonorepoBuilder\Testing\PackageDependency\UsedPackagesResolver;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ComposerJsonRequireUpdater
{
    public function __construct(
        private JsonFileManager $jsonFileManager,
        private SymfonyStyle $symfonyStyle,
        private ComposerVersionManipulator $composerVersionManipulator,
        private UsedPackagesResolver $usedPackagesResolver,
        private ConsoleDiffer $consoleDiffer
    ) {
    }

    public function processPackage(SmartFileInfo $packageFileInfo): void
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

        $packageComposerJson = $this->composerVersionManipulator->decorateAsteriskVersionForUsedPackages(
            $packageComposerJson,
            $usedPackageNames
        );

        $oldComposerJsonContents = $packageFileInfo->getContents();

        $newComposerJsonContents = $this->jsonFileManager->printJsonToFileInfo($packageComposerJson, $packageFileInfo);

        $message = sprintf('File "%s" was updated', $packageFileInfo->getRelativeFilePathFromCwd());
        $this->symfonyStyle->title($message);

        $diff = $this->consoleDiffer->diff($oldComposerJsonContents, $newComposerJsonContents);
        $this->symfonyStyle->writeln($diff);

        $this->symfonyStyle->newLine(2);
    }
}
