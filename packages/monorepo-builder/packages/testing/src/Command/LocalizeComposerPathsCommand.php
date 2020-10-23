<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Testing\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Package\PackageNamesProvider;
use Symplify\MonorepoBuilder\Testing\ComposerJson\ComposerJsonSymlinker;
use Symplify\MonorepoBuilder\Testing\ComposerJson\ComposerVersionManipulator;
use Symplify\MonorepoBuilder\Testing\PackageDependency\UsedPackagesResolver;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;

final class LocalizeComposerPathsCommand extends Command
{
    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

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
        ComposerJsonProvider $composerJsonProvider,
        PackageNamesProvider $packageNamesProvider,
        JsonFileManager $jsonFileManager,
        SymfonyStyle $symfonyStyle,
        ComposerJsonSymlinker $composerJsonSymlinker,
        ComposerVersionManipulator $composerVersionManipulator,
        UsedPackagesResolver $usedPackagesResolver
    ) {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->packageNamesProvider = $packageNamesProvider;
        $this->jsonFileManager = $jsonFileManager;
        $this->symfonyStyle = $symfonyStyle;
        $this->composerJsonSymlinker = $composerJsonSymlinker;
        $this->composerVersionManipulator = $composerVersionManipulator;
        $this->usedPackagesResolver = $usedPackagesResolver;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Set mutual package paths to local packages - use only for local package testing');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rootFileInfo = $this->composerJsonProvider->getRootFileInfo();

        $packagesFileInfos = $this->composerJsonProvider->getPackagesComposerFileInfos();
        foreach ($packagesFileInfos as $packagesFileInfo) {
            $this->processPackage($packagesFileInfo, $rootFileInfo);
        }

        $this->symfonyStyle->success('Package paths have been updated');

        return ShellCode::SUCCESS;
    }

    private function processPackage(SmartFileInfo $packageFileInfo, SmartFileInfo $mainComposerJsonFileInfo): void
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
