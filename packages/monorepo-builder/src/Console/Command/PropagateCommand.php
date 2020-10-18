<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\DependencyUpdater;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\VersionValidator;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PropagateCommand extends Command
{
    /**
     * @var string[]
     */
    private const POSSIBLE_CONFIG_FILE_PATHS = [
        'monorepo-builder.yaml',
        'monorepo-builder.yml',
        'monorepo-builder.php',
    ];

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
     * @var DependencyUpdater
     */
    private $dependencyUpdater;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        VersionValidator $versionValidator,
        ComposerJsonProvider $composerJsonProvider,
        DependencyUpdater $dependencyUpdater
    ) {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
        $this->versionValidator = $versionValidator;
        $this->composerJsonProvider = $composerJsonProvider;
        $this->dependencyUpdater = $dependencyUpdater;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
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
            if (! isset($filesToVersion['composer.json'])) {
                // nothing to propagate
                continue;
            }

            // update all other files to root composer.json version
            $newVersion = $filesToVersion['composer.json'];
            unset($filesToVersion['composer.json']);

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
        foreach (self::POSSIBLE_CONFIG_FILE_PATHS as $possibleConfigFilepath) {
            if (! isset($filesToVersion[$possibleConfigFilepath])) {
                continue;
            }

            $message = sprintf(
                'Update "%s" to "%s" version in "%s" file manually',
                $packageName,
                $newVersion,
                $possibleConfigFilepath
            );
            $this->symfonyStyle->warning($message);

            unset($filesToVersion[$possibleConfigFilepath]);
        }

        return $filesToVersion;
    }
}
