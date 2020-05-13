<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\Console\Reporter\ConflictingPackageVersionsReporter;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\VersionValidator;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class ValidateCommand extends Command
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

    public function __construct(
        SymfonyStyle $symfonyStyle,
        ComposerJsonProvider $composerJsonProvider,
        VersionValidator $versionValidator,
        ConflictingPackageVersionsReporter $conflictingPackageVersionsReporter
    ) {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
        $this->versionValidator = $versionValidator;
        $this->composerJsonProvider = $composerJsonProvider;
        $this->conflictingPackageVersionsReporter = $conflictingPackageVersionsReporter;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Validates synchronized versions in "composer.json" in all found packages.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $conflictingPackageVersions = $this->versionValidator->findConflictingPackageVersionsInFileInfos(
            $this->composerJsonProvider->getRootAndPackageFileInfos()
        );
        if ($conflictingPackageVersions === []) {
            $this->symfonyStyle->success('All packages "composer.json" files use same package versions.');

            return ShellCode::SUCCESS;
        }

        $this->conflictingPackageVersionsReporter->report($conflictingPackageVersions);

        return ShellCode::ERROR;
    }
}
