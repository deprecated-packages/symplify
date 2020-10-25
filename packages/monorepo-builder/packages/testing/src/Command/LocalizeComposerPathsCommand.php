<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Testing\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Testing\ComposerJsonUpdater;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class LocalizeComposerPathsCommand extends Command
{
    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var ComposerJsonUpdater
     */
    private $composerJsonUpdater;

    public function __construct(
        ComposerJsonProvider $composerJsonProvider,
        SymfonyStyle $symfonyStyle,
        ComposerJsonUpdater $composerJsonUpdater
    ) {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->symfonyStyle = $symfonyStyle;

        parent::__construct();

        $this->composerJsonUpdater = $composerJsonUpdater;
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
            $this->composerJsonUpdater->processPackage($packagesFileInfo, $rootFileInfo);
        }

        $this->symfonyStyle->success('Package paths have been updated');

        return ShellCode::SUCCESS;
    }
}
