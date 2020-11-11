<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\MonorepoBuilder\DependencyUpdater;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\ValueObject\File;
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

    public function __construct(
        VersionValidator $versionValidator,
        ComposerJsonProvider $composerJsonProvider,
        DependencyUpdater $dependencyUpdater
    ) {
        parent::__construct();

        $this->versionValidator = $versionValidator;
        $this->composerJsonProvider = $composerJsonProvider;
        $this->dependencyUpdater = $dependencyUpdater;
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

            $filesToVersion = $this->processManualConfigFiles($filesToVersion, $packageName, $newVersion);
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

    /**
     * @param array<string, string> $filesToVersion
     * @return array<string, string>
     */
    private function processManualConfigFiles(array $filesToVersion, string $packageName, string $newVersion): array
    {
        if (! isset($filesToVersion[File::CONFIG])) {
            return $filesToVersion;
        }

        $message = sprintf(
            'Update "%s" to "%s" version in "%s" file manually',
            $packageName,
            $newVersion,
            File::CONFIG
        );
        $this->symfonyStyle->warning($message);

        unset($filesToVersion[File::CONFIG]);

        return $filesToVersion;
    }
}
