<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\MonorepoBuilder\DependencyUpdater;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\ValueObject\File;
use Symplify\MonorepoBuilder\VersionPropagator;
use Symplify\MonorepoBuilder\VersionValidator;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PropagateCommand extends AbstractSymplifyCommand
{
    /**
     * @var VersionValidator
     */
    private $versionValidator;

    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @var DependencyUpdater
     */
    private $dependencyUpdater;

    /**
     * @var VersionPropagator
     */
    private $versionPropagator;

    public function __construct(
        VersionValidator $versionValidator,
        ComposerJsonProvider $composerJsonProvider,
        DependencyUpdater $dependencyUpdater,
        VersionPropagator $versionPropagator
    ) {
        parent::__construct();

        $this->versionValidator = $versionValidator;
        $this->composerJsonProvider = $composerJsonProvider;
        $this->dependencyUpdater = $dependencyUpdater;
        $this->versionPropagator = $versionPropagator;
    }

    protected function configure(): void
    {
        $this->setDescription(
            'Propagate versions from root "composer.json" to all packages, the opposite of "merge" command'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $conflictingPackageVersions = $this->versionValidator->findConflictingPackageVersionsInFileInfos(
            $this->composerJsonProvider->getRootAndPackageFileInfos()
        );

        foreach ($conflictingPackageVersions as $packageName => $filesToVersion) {
            if (! isset($filesToVersion[File::COMPOSER_JSON])) {
                // nothing to propagate
                continue;
            }

            // update all other files to root composer.json version
            $newVersion = $filesToVersion[File::COMPOSER_JSON];
            unset($filesToVersion[File::COMPOSER_JSON]);

            $filesToVersion = $this->versionPropagator->processManualConfigFiles(
                $filesToVersion,
                $packageName,
                $newVersion
            );
            $fileToVersionKeys = array_keys($filesToVersion);

            foreach ($fileToVersionKeys as $filePath) {
                $this->dependencyUpdater->updateFileInfosWithPackagesAndVersion(
                    [new SmartFileInfo($filePath)],
                    [$packageName],
                    $newVersion
                );
            }
        }

        $this->symfonyStyle->success(
            'Root "composer.json" versions are now propagated to all package "composer.json" files.'
        );

        return ShellCode::SUCCESS;
    }
}
