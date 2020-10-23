<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\Console\Reporter\ConflictingPackageVersionsReporter;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Merge\Application\MergedAndDecoratedComposerJsonFactory;
use Symplify\MonorepoBuilder\VersionValidator;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class MergeCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

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

    /**
     * @var ComposerJsonFactory
     */
    private $composerJsonFactory;

    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    /**
     * @var MergedAndDecoratedComposerJsonFactory
     */
    private $mergedAndDecoratedComposerJsonFactory;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        VersionValidator $versionValidator,
        ComposerJsonProvider $composerJsonProvider,
        ConflictingPackageVersionsReporter $conflictingPackageVersionsReporter,
        ComposerJsonFactory $composerJsonFactory,
        JsonFileManager $jsonFileManager,
        MergedAndDecoratedComposerJsonFactory $mergedAndDecoratedComposerJsonFactory
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->versionValidator = $versionValidator;
        $this->composerJsonProvider = $composerJsonProvider;
        $this->conflictingPackageVersionsReporter = $conflictingPackageVersionsReporter;
        $this->composerJsonFactory = $composerJsonFactory;
        $this->jsonFileManager = $jsonFileManager;
        $this->mergedAndDecoratedComposerJsonFactory = $mergedAndDecoratedComposerJsonFactory;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Merge "composer.json" from all found packages to root one');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->ensureNoConflictingPackageVersions();

        $mainComposerJsonFilePath = getcwd() . '/composer.json';
        $mainComposerJson = $this->composerJsonFactory->createFromFilePath($mainComposerJsonFilePath);
        $packageFileInfos = $this->composerJsonProvider->getPackagesComposerFileInfos();

        $this->mergedAndDecoratedComposerJsonFactory->createFromRootConfigAndPackageFileInfos(
            $mainComposerJson,
            $packageFileInfos
        );

        $this->jsonFileManager->saveComposerJsonToFilePath($mainComposerJson, $mainComposerJsonFilePath);
        $this->symfonyStyle->success('Main "composer.json" was updated.');

        return ShellCode::SUCCESS;
    }

    private function ensureNoConflictingPackageVersions(): void
    {
        $conflictingPackageVersions = $this->versionValidator->findConflictingPackageVersionsInFileInfos(
            $this->composerJsonProvider->getPackagesComposerFileInfos()
        );

        if (count($conflictingPackageVersions) === 0) {
            return;
        }

        $this->conflictingPackageVersionsReporter->report($conflictingPackageVersions);

        throw new ShouldNotHappenException('Fix conflicting package version first');
    }
}
