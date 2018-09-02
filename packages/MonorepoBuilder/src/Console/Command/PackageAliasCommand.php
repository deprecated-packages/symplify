<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\DevMasterAliasUpdater;
use Symplify\MonorepoBuilder\PackageComposerFinder;
use Symplify\MonorepoBuilder\Utils\Utils;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

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
     * @var Utils
     */
    private $utils;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        PackageComposerFinder $packageComposerFinder,
        DevMasterAliasUpdater $devMasterAliasUpdater,
        Utils $utils
    ) {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
        $this->packageComposerFinder = $packageComposerFinder;
        $this->devMasterAliasUpdater = $devMasterAliasUpdater;
        $this->utils = $utils;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Updates branch alias in "composer.json" all found packages');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $composerPackageFiles = $this->packageComposerFinder->getPackageComposerFiles();
        if (! count($composerPackageFiles)) {
            $this->symfonyStyle->error('No "composer.json" were found in packages.');
            return 1;
        }

        $alias = $this->getExpectedAlias();

        $this->devMasterAliasUpdater->updateFileInfosWithAlias($composerPackageFiles, $alias);
        $this->symfonyStyle->success(sprintf('Alias "dev-master" was updated to "%s" in all packages.', $alias));

        // success
        return 0;
    }

    private function getExpectedAlias(): string
    {
        $lastTag = exec('git describe --abbrev=0 --tags');

        return $this->utils->getNextAliasFormat($lastTag)->getVersionString();
    }
}
