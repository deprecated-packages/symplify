<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\ComposerJsonMerger;
use Symplify\MonorepoBuilder\Console\Reporter\ConflictingPackageVersionsReporter;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Package\PackageComposerJsonMerger;
use Symplify\MonorepoBuilder\VersionValidator;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class MergeCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var PackageComposerJsonMerger
     */
    private $packageComposerJsonMerger;

    /**
     * @var ComposerJsonMerger
     */
    private $composerJsonMerger;

    /**
     * @var VersionValidator
     */
    private $versionValidator;

    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @var ConflictingPackageVersionsReporter
     */
    private $conflictingPackageVersionsReporter;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        PackageComposerJsonMerger $packageComposerJsonMerger,
        ComposerJsonMerger $composerJsonMerger,
        VersionValidator $versionValidator,
        ComposerJsonProvider $composerJsonProvider,
        ConflictingPackageVersionsReporter $conflictingPackageVersionsReporter
    ) {
        parent::__construct();
        $this->symfonyStyle = $symfonyStyle;
        $this->packageComposerJsonMerger = $packageComposerJsonMerger;
        $this->composerJsonMerger = $composerJsonMerger;
        $this->versionValidator = $versionValidator;
        $this->composerJsonProvider = $composerJsonProvider;

        $this->conflictingPackageVersionsReporter = $conflictingPackageVersionsReporter;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Merge "composer.json" from all found packages to root one');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $conflictingPackageVersions = $this->versionValidator->findConflictingPackageVersionsInFileInfos(
            $this->composerJsonProvider->getPackagesFileInfos()
        );
        if (count($conflictingPackageVersions) > 0) {
            $this->conflictingPackageVersionsReporter->report($conflictingPackageVersions);

            return ShellCode::ERROR;
        }

        $mergedComposerJons = $this->packageComposerJsonMerger->mergeFileInfos(
            $this->composerJsonProvider->getPackagesFileInfos()
        );

        if ($mergedComposerJons->isEmpty()) {
            $this->symfonyStyle->note('Nothing to merge.');

            return ShellCode::SUCCESS;
        }

        $this->composerJsonMerger->mergeJsonToRootFilePathAndSave($mergedComposerJons, getcwd() . '/composer.json');

        $this->symfonyStyle->success('Main "composer.json" was updated.');

        return ShellCode::SUCCESS;
    }
}
