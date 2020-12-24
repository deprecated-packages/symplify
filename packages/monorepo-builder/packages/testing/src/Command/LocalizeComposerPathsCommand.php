<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Testing\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Testing\ComposerJsonRepositoriesUpdater;
use Symplify\MonorepoBuilder\Testing\ComposerJsonRequireUpdater;
use Symplify\MonorepoBuilder\Testing\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;

final class LocalizeComposerPathsCommand extends AbstractSymplifyCommand
{
    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @var ComposerJsonRequireUpdater
     */
    private $composerJsonRequireUpdater;

    /**
     * @var ComposerJsonRepositoriesUpdater
     */
    private $composerJsonRepositoriesUpdater;

    public function __construct(
        ComposerJsonProvider $composerJsonProvider,
        ComposerJsonRequireUpdater $composerJsonRequireUpdater,
        ComposerJsonRepositoriesUpdater $composerJsonRepositoriesUpdater
    ) {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->composerJsonRequireUpdater = $composerJsonRequireUpdater;
        $this->composerJsonRepositoriesUpdater = $composerJsonRepositoriesUpdater;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Set mutual package paths to local packages - use for pre-split package testing');
        $this->addArgument(
            Option::PACKAGE_COMPOSER_JSON,
            InputArgument::REQUIRED,
            'Path to package "composer.json"'
        );
        $this->addOption(
            Option::SYMLINK,
            null,
            InputOption::VALUE_NONE,
            'Localize composer paths with symlinks'
        );
        $this->addOption(
            Option::SKIP_DEV_BRANCH_UPDATE,
            null,
            InputOption::VALUE_NONE,
            'Do not update all "require" and "require-dev" entries to the "dev-master" branch of all local packages'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $packageComposerJson = (string) $input->getArgument(Option::PACKAGE_COMPOSER_JSON);
        $this->fileSystemGuard->ensureFileExists($packageComposerJson, __METHOD__);

        $packageComposerJsonFileInfo = new SmartFileInfo($packageComposerJson);
        $rootComposerJson = $this->composerJsonProvider->getRootComposerJson();

        // 1. update "require" to "*" for all local packages
        $skipDevBranchUpdate = (bool) $input->getOption(Option::SKIP_DEV_BRANCH_UPDATE);
        if (! $skipDevBranchUpdate) {
            $packagesFileInfos = $this->composerJsonProvider->getPackagesComposerFileInfos();
            foreach ($packagesFileInfos as $packageFileInfo) {
                $this->composerJsonRequireUpdater->processPackage($packageFileInfo);
            }
        }

        // 2. update "repository" to "*" for current composer.json
        // $symlink => `false` is needed for testing on GitHub Actions:
        // we need hard copy of files,
        // as in normal composer install of standalone package
        // $symlink => `true` is needed to point to local packages
        // during development, avoiding Packagist
        $symlink = (bool) $input->getOption(Option::SYMLINK);
        $this->composerJsonRepositoriesUpdater->processPackage(
            $packageComposerJsonFileInfo,
            $rootComposerJson,
            $symlink
        );

        $message = sprintf(
            'Package paths in "%s" have been updated',
            $packageComposerJsonFileInfo->getRelativeFilePathFromCwd()
        );
        $this->symfonyStyle->success($message);

        return ShellCode::SUCCESS;
    }
}
