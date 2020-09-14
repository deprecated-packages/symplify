<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Symplify\MonorepoBuilder\DevMasterAliasUpdater;
use Symplify\MonorepoBuilder\Finder\PackageComposerFinder;
use Symplify\MonorepoBuilder\Utils\VersionUtils;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class PackageAliasCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var PackageComposerFinder
     */
    private $packageComposerFinder;

    /**
     * @var DevMasterAliasUpdater
     */
    private $devMasterAliasUpdater;

    /**
     * @var VersionUtils
     */
    private $versionUtils;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        PackageComposerFinder $packageComposerFinder,
        DevMasterAliasUpdater $devMasterAliasUpdater,
        VersionUtils $versionUtils
    ) {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
        $this->packageComposerFinder = $packageComposerFinder;
        $this->devMasterAliasUpdater = $devMasterAliasUpdater;
        $this->versionUtils = $versionUtils;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Updates branch alias in "composer.json" all found packages');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $composerPackageFiles = $this->packageComposerFinder->getPackageComposerFiles();
        if (count($composerPackageFiles) === 0) {
            $this->symfonyStyle->error('No "composer.json" were found in packages.');
            return ShellCode::ERROR;
        }

        $expectedAlias = $this->getExpectedAlias();

        $this->devMasterAliasUpdater->updateFileInfosWithAlias($composerPackageFiles, $expectedAlias);

        $message = sprintf('Alias "dev-master" was updated to "%s" in all packages.', $expectedAlias);
        $this->symfonyStyle->success($message);

        return ShellCode::SUCCESS;
    }

    private function getExpectedAlias(): string
    {
        $process = new Process(['git', 'describe', '--abbrev=0', '--tags']);
        $process->run();

        $output = $process->getOutput();

        return $this->versionUtils->getNextAliasFormat($output);
    }
}
