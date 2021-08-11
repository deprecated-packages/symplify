<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\MonorepoBuilder\DevMasterAliasUpdater;
use Symplify\MonorepoBuilder\Finder\PackageComposerFinder;
use Symplify\MonorepoBuilder\Git\ExpectedAliasResolver;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;

final class PackageAliasCommand extends AbstractSymplifyCommand
{
    public function __construct(
        private PackageComposerFinder $packageComposerFinder,
        private DevMasterAliasUpdater $devMasterAliasUpdater,
        private ExpectedAliasResolver $expectedAliasResolver
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Updates branch alias in "composer.json" all found packages');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $composerPackageFiles = $this->packageComposerFinder->getPackageComposerFiles();
        if ($composerPackageFiles === []) {
            $this->symfonyStyle->error('No "composer.json" were found in packages.');
            return self::FAILURE;
        }

        $expectedAlias = $this->expectedAliasResolver->resolve();

        $this->devMasterAliasUpdater->updateFileInfosWithAlias($composerPackageFiles, $expectedAlias);

        $message = sprintf('Alias was updated to "%s" in all packages.', $expectedAlias);
        $this->symfonyStyle->success($message);

        return self::SUCCESS;
    }
}
