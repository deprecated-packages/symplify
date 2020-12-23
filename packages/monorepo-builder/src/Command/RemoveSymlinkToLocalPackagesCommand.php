<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Testing\ComposerJsonRepositoriesRemover;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;

final class RemoveSymlinkToLocalPackagesCommand extends AbstractSymplifyCommand
{
    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @var ComposerJsonRepositoriesRemover
     */
    private $composerJsonRepositoriesRemover;

    public function __construct(
        ComposerJsonProvider $composerJsonProvider,
        ComposerJsonRepositoriesRemover $composerJsonRepositoriesRemover
    ) {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->composerJsonRepositoriesRemover = $composerJsonRepositoriesRemover;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Remove paths to local packages as repositories in every composer.json');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rootComposerJson = $this->composerJsonProvider->getRootComposerJson();

        $packagesFileInfos = $this->composerJsonProvider->getPackagesComposerFileInfos();
        foreach ($packagesFileInfos as $packageFileInfo) {
            // $symlink => `true`: when executing a package,
            // any modification to another local package can be seen immediately
            $this->composerJsonRepositoriesRemover->processPackage(
                $packageFileInfo,
                $rootComposerJson,
                true
            );
        }

        $message = 'The composer.json for all packages has been updated, removing the symlink to their required local packages';
        $this->symfonyStyle->success($message);

        return ShellCode::SUCCESS;
    }
}
