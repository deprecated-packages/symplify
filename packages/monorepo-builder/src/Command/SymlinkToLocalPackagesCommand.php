<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Testing\ComposerJsonRepositoriesUpdater;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SymlinkToLocalPackagesCommand extends AbstractSymplifyCommand
{
    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @var ComposerJsonRepositoriesUpdater
     */
    private $composerJsonRepositoriesUpdater;

    public function __construct(
        ComposerJsonProvider $composerJsonProvider,
        ComposerJsonRepositoriesUpdater $composerJsonRepositoriesUpdater
    ) {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->composerJsonRepositoriesUpdater = $composerJsonRepositoriesUpdater;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Add paths to local packages as repositories in every composer.json, to symlink to their local source code and avoid fetching them from Packagist');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rootComposerJson = $this->composerJsonProvider->getRootComposerJson();

        $packagesFileInfos = $this->composerJsonProvider->getPackagesComposerFileInfos();
        foreach ($packagesFileInfos as $packageFileInfo) {
            // $symlink => `true`: when executing a package,
            // any modification to another local package can be seen immediately
            $this->composerJsonRepositoriesUpdater->processPackage(
                $packageFileInfo,
                $rootComposerJson,
                true
            );
        }

        $message = 'The composer.json for all packages has been updated, symlinking to their required local packages';
        $this->symfonyStyle->success($message);

        return ShellCode::SUCCESS;
    }
}
